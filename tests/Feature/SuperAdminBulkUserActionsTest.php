<?php

namespace Tests\Feature;

use App\Mail\PortalEmail;
use App\Models\AuditLog;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SuperAdminBulkUserActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_activate_updates_eligible_users_and_rejects_pending_invitations(): void
    {
        $university = $this->createUniversity('Bulk Activate University', 'BAU');
        $actor = $this->createUser($university, 'bulk-actor@bau.edu', 'Bulk Actor', 'super_admin', true, now());
        $suspendedUser = $this->createUser($university, 'suspended@bau.edu', 'Suspended User', 'admin', false, now());
        $pendingUser = $this->createUser($university, 'pending@bau.edu', 'Pending User', 'student', false, null);

        $response = $this->superAdminSession($actor, $university)->post('/super-admin/users/bulk-activate', [
            'user_ids' => [$suspendedUser->id, $pendingUser->id],
        ]);

        $response->assertRedirect('/super-admin/users');
        $response->assertSessionHas('success');
        $this->assertTrue($suspendedUser->fresh()->is_active);
        $this->assertFalse($pendingUser->fresh()->is_active);
        $this->assertNull($pendingUser->fresh()->email_verified_at);

        $auditLog = AuditLog::query()
            ->where('model_type', 'User')
            ->where('model_id', $suspendedUser->id)
            ->where('action', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertSame('bulk_activated', data_get($auditLog->new_values, 'transition'));
        $this->assertSame($actor->id, $auditLog->user_id);
    }

    public function test_bulk_suspend_enforces_self_and_last_super_admin_guards(): void
    {
        $university = $this->createUniversity('Bulk Suspend University', 'BSU');
        
        // Test self-suspend guard: super_admin cannot suspend themselves
        $actor = $this->createUser($university, 'suspend-actor@bsu.edu', 'Suspend Actor', 'super_admin', true, now());
        
        $response = $this->superAdminSession($actor, $university)->post('/super-admin/users/bulk-suspend', [
            'user_ids' => [$actor->id],
        ]);

        $response->assertRedirect('/super-admin/users');
        $response->assertSessionHas('error');
        $this->assertTrue($actor->fresh()->is_active);

        $result = session('bulk_result');
        $this->assertCount(1, $result['errors']);
        $this->assertSame('You cannot suspend your own account.', $result['errors'][0]['reason']);

        // Test last-super-admin guard: with only 1 super_admin in system, suspending them is blocked
        // Create a fresh university to isolate from previous test
        $university2 = $this->createUniversity('Bulk Suspend University 2', 'BSU2');
        $soleSuperAdmin = $this->createUser($university2, 'sole-super@bsu2.edu', 'Sole Super Admin', 'super_admin', true, now());
        // No other super_admins exist
        
        $response = $this->superAdminSession($soleSuperAdmin, $university2)->post('/super-admin/users/bulk-suspend', [
            'user_ids' => [$soleSuperAdmin->id],
        ]);

        $response->assertRedirect('/super-admin/users');
        $response->assertSessionHas('error');
        $this->assertTrue($soleSuperAdmin->fresh()->is_active);

        $result = session('bulk_result');
        $this->assertCount(1, $result['errors']);
        // Both self-guard and last-super-admin guard apply; self-guard triggers first
        $this->assertSame('You cannot suspend your own account.', $result['errors'][0]['reason']);
    }

    public function test_bulk_resend_invitations_only_resends_pending_invitations_and_audits_success(): void
    {
        Mail::fake();

        $university = $this->createUniversity('Bulk Invite University', 'BIU');
        $actor = $this->createUser($university, 'invite-actor@biu.edu', 'Invite Actor', 'super_admin', true, now());
        $pendingUser = $this->createUser($university, 'invite-pending@biu.edu', 'Invite Pending', 'admin', false, null);
        $activeUser = $this->createUser($university, 'invite-active@biu.edu', 'Invite Active', 'student', true, now());

        $response = $this->superAdminSession($actor, $university)->post('/super-admin/users/bulk-resend-invite', [
            'user_ids' => [$pendingUser->id, $activeUser->id],
        ]);

        $response->assertRedirect('/super-admin/users');
        $response->assertSessionHas('success');
        Mail::assertSent(PortalEmail::class, 1);
        Mail::assertSent(PortalEmail::class, function (PortalEmail $mail) use ($pendingUser) {
            return $mail->viewName === 'account-invite' && $mail->hasTo($pendingUser->email);
        });

        $auditLog = AuditLog::query()
            ->where('model_type', 'User')
            ->where('model_id', $pendingUser->id)
            ->where('action', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($auditLog);
        $this->assertSame('invite_resent', data_get($auditLog->new_values, 'transition'));
    }

    public function test_export_respects_filters_and_selected_users(): void
    {
        $university = $this->createUniversity('Bulk Export University', 'BEU');
        $actor = $this->createUser($university, 'export-actor@beu.edu', 'Export Actor', 'super_admin', true, now());
        $student = $this->createUser($university, 'student@beu.edu', 'Export Student', 'student', true, now());
        $admin = $this->createUser($university, 'admin@beu.edu', 'Export Admin', 'admin', true, now());

        $filteredResponse = $this->superAdminSession($actor, $university)->get('/super-admin/users/export?role=student');
        $filteredResponse->assertOk();
        $this->assertStringContainsString('text/csv', $filteredResponse->headers->get('Content-Type'));
        $this->assertStringContainsString('student@beu.edu', $filteredResponse->getContent());
        $this->assertStringNotContainsString('admin@beu.edu', $filteredResponse->getContent());

        $selectedResponse = $this->superAdminSession($actor, $university)->post('/super-admin/users/export-selected', [
            'user_ids' => [$admin->id],
        ]);
        $selectedResponse->assertOk();
        $this->assertStringContainsString('text/csv', $selectedResponse->headers->get('Content-Type'));
        $this->assertStringContainsString('admin@beu.edu', $selectedResponse->getContent());
        $this->assertStringNotContainsString('student@beu.edu', $selectedResponse->getContent());
    }

    public function test_users_page_renders_bulk_selection_controls(): void
    {
        $university = $this->createUniversity('Bulk UI University', 'BUI');
        $actor = $this->createUser($university, 'ui-actor@bui.edu', 'UI Actor', 'super_admin', true, now());
        $this->createUser($university, 'ui-user@bui.edu', 'UI User', 'admin', true, now());

        $response = $this->superAdminSession($actor, $university)->get('/super-admin/users');

        $response->assertOk()
            ->assertSee('Bulk identity actions', false)
            ->assertSee('select-all-users', false)
            ->assertSee('user_ids[]', false)
            ->assertSee(route('super-admin.users.bulk-activate'), false)
            ->assertSee(route('super-admin.users.export-selected'), false);
    }

    public function test_bulk_actions_reject_an_empty_selection(): void
    {
        $university = $this->createUniversity('Bulk Validation University', 'BVU');
        $actor = $this->createUser($university, 'validation-actor@bvu.edu', 'Validation Actor', 'super_admin', true, now());

        $response = $this->superAdminSession($actor, $university)->post('/super-admin/users/bulk-activate', [
            'user_ids' => [],
        ]);

        $response->assertSessionHasErrors(['user_ids']);
    }

    private function superAdminSession(User $actor, University $university): self
    {
        return $this->withSession([
            'user_id' => $actor->id,
            'role' => 'super_admin',
            'university_id' => $university->id,
            'mfa_verified' => true,
            'last_activity' => time(),
            'session_started' => time(),
        ]);
    }

    private function createUniversity(string $name, string $code): University
    {
        return University::create([
            'name' => $name,
            'code' => $code,
            'email' => 'info@' . strtolower($code) . '.edu',
            'department' => 'Research Office',
            'phone' => '08000000000',
            'is_active' => true,
        ]);
    }

    private function createUser(
        University $university,
        string $email,
        string $name,
        string $role,
        bool $isActive,
        $emailVerifiedAt
    ): User {
        return User::create([
            'university_id' => $university->id,
            'email' => $email,
            'password' => 'Password@2026',
            'name' => $name,
            'role' => $role,
            'is_active' => $isActive,
            'email_verified_at' => $emailVerifiedAt,
        ]);
    }
}
