<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Services\WorkspaceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthMutation
{
    public function __construct(
        private readonly WorkspaceService $workspaceService,
    ) {
    }

    public function login($_, array $args): array
    {
        if (! Auth::attempt([
            'email' => $args['email'],
            'password' => $args['password'],
        ])) {
            throw new \InvalidArgumentException('Invalid credentials.');
        }

        request()->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();
        $workspace = $this->workspaceService->currentForUser($user);

        return [
            'token' => request()->session()->token(),
            'refreshToken' => null,
            'user' => $user->loadMissing('memberships'),
            'workspace' => $workspace,
        ];
    }

    public function register($_, array $args): array
    {
        Validator::make($args['input'], [
            'name' => ['required', 'string', 'max:255'],
            'workspaceName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', PasswordRule::min(8)],
        ])->validate();

        $user = DB::transaction(function () use ($args): User {
            $user = User::query()->create([
                'name' => $args['input']['name'],
                'email' => $args['input']['email'],
                'job_title' => 'Workspace Admin',
                'avatar_path' => 'images/glacier/avatars/profile-main.png',
                'timezone' => 'Asia/Jakarta',
                'password' => Hash::make($args['input']['password']),
                'email_verified_at' => now(),
            ]);

            $this->workspaceService->createInitialWorkspace($user, $args['input']['workspaceName']);

            return $user;
        });

        Auth::login($user);
        request()->session()->regenerate();
        $workspace = $this->workspaceService->currentForUser($user);

        return [
            'token' => request()->session()->token(),
            'refreshToken' => null,
            'user' => $user->loadMissing('memberships'),
            'workspace' => $workspace,
        ];
    }
}
