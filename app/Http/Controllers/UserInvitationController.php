<?php

namespace App\Http\Controllers;

use App\Services\UserInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class UserInvitationController extends BaseController
{
    public function __construct(private UserInvitationService $userInvitationService)
    {
    }

    public function show(string $token): View
    {
        $payload = $this->userInvitationService->getInvitation($token);

        abort_unless($payload, 404);

        return view('auth.complete-invitation', [
            'token' => $token,
            'user' => $payload['user']->load('university'),
            'roleLabel' => $this->userInvitationService->roleLabel($payload['user']->role),
            'expiresAt' => $payload['invitation']['expires_at'],
        ]);
    }

    public function complete(Request $request, string $token): View|RedirectResponse
    {
        $payload = $this->userInvitationService->getInvitation($token);

        abort_unless($payload, 404);

        $validated = $request->validate($this->userInvitationService->completionRules($payload['user']));

        try {
            $user = $this->userInvitationService->completeInvitation($token, $validated);
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors([
                'invite' => $exception->getMessage(),
            ]);
        }

        return view('auth.complete-invitation-success', [
            'user' => $user,
            'roleLabel' => $this->userInvitationService->roleLabel($user->role),
        ]);
    }
}