<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class BulkUserActionService
{
    public const MAX_BATCH_SIZE = 100;

    public function __construct(private UserInvitationService $invitationService)
    {
    }

    public function activate(array $userIds, ?User $actor): array
    {
        return $this->changeStatus($userIds, $actor, true, 'bulk_activated');
    }

    public function suspend(array $userIds, ?User $actor): array
    {
        return $this->changeStatus($userIds, $actor, false, 'bulk_suspended');
    }

    public function resendInvitations(array $userIds, ?User $actor): array
    {
        $result = $this->baseResult('resend_invites');
        $userIds = $this->normaliseIds($userIds);
        $result['total'] = count($userIds);

        foreach ($userIds as $userId) {
            $user = User::find($userId);

            if (!$user) {
                $this->addError($result, $userId, 'User not found.');
                continue;
            }

            if ($user->is_active || $user->email_verified_at !== null) {
                $result['skipped']++;
                continue;
            }

            try {
                $oldValues = $user->toArray();
                $this->invitationService->resendInvitation($user);
                $freshUser = $user->fresh();
                $this->writeAuditLog(
                    $freshUser?->university,
                    $actor,
                    $user->id,
                    $oldValues,
                    ($freshUser?->toArray() ?? $user->toArray()) + ['transition' => 'invite_resent', 'batch_action' => 'resend_invites']
                );
                $result['processed']++;
            } catch (Throwable $exception) {
                $this->addError($result, $userId, 'Invitation could not be resent.');
            }
        }

        return $result;
    }

    private function changeStatus(array $userIds, ?User $actor, bool $activate, string $transition): array
    {
        $result = $this->baseResult($transition);
        $userIds = $this->normaliseIds($userIds);
        $result['total'] = count($userIds);

        foreach ($userIds as $userId) {
            $user = User::find($userId);

            if (!$user) {
                $this->addError($result, $userId, 'User not found.');
                continue;
            }

            if ($activate && !$user->is_active && $user->email_verified_at === null) {
                $this->addError($result, $userId, 'Pending invitations must complete onboarding before activation.');
                continue;
            }

            if ($activate && $user->is_active) {
                $result['skipped']++;
                continue;
            }

            if (!$activate && $user->is_active && $actor && (int) $user->id === (int) $actor->id) {
                $this->addError($result, $userId, 'You cannot suspend your own account.');
                continue;
            }

            if (!$activate && $user->is_active && $user->role === 'super_admin' && $this->activeSuperAdminCount() <= 1) {
                $this->addError($result, $userId, 'At least one active super admin account must remain.');
                continue;
            }

            if (!$activate && !$user->is_active) {
                $result['skipped']++;
                continue;
            }

            $oldValues = $user->toArray();

            try {
                DB::transaction(function () use ($user, $activate, $actor, $oldValues, $transition): void {
                    $user->update(['is_active' => $activate]);
                    $freshUser = $user->fresh();
                    $this->writeAuditLog(
                        $freshUser?->university,
                        $actor,
                        $user->id,
                        $oldValues,
                        ($freshUser?->toArray() ?? $user->toArray()) + ['transition' => $transition, 'batch_action' => $transition]
                    );
                });

                $result['processed']++;
                $result['changed']++;
            } catch (Throwable $exception) {
                $this->addError($result, $userId, 'Account status could not be updated.');
            }
        }

        return $result;
    }

    private function normaliseIds(array $userIds): array
    {
        $normalised = [];

        foreach ($userIds as $userId) {
            $id = filter_var($userId, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);

            if ($id !== false) {
                $normalised[(int) $id] = (int) $id;
            }
        }

        return array_values($normalised);
    }

    private function baseResult(string $action): array
    {
        return [
            'action' => $action,
            'total' => 0,
            'processed' => 0,
            'changed' => 0,
            'skipped' => 0,
            'errors' => [],
            'audit_ids' => [],
        ];
    }

    private function addError(array &$result, int $userId, string $reason): void
    {
        $result['errors'][] = [
            'user_id' => $userId,
            'reason' => $reason,
        ];
    }

    private function activeSuperAdminCount(): int
    {
        return User::where('role', 'super_admin')
            ->where('is_active', true)
            ->count();
    }

    private function writeAuditLog(
        ?\App\Models\University $university,
        ?User $actor,
        int $userId,
        array $oldValues,
        array $newValues
    ): void {
        if (!$university) {
            return;
        }

        AuditLog::logAction(
            $university,
            $actor,
            'User',
            $userId,
            'updated',
            $oldValues,
            $newValues
        );
    }
}
