<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileMutation
{
    public function updateProfile($_, array $args): User
    {
        /** @var User $user */
        $user = Auth::user();

        $user->fill([
            'name' => $args['input']['name'] ?? $user->name,
            'avatar_path' => $args['input']['avatarUrl'] ?? $user->avatar_path,
            'timezone' => $args['input']['timezone'] ?? $user->timezone,
            'bio' => $args['input']['bio'] ?? $user->bio,
        ]);

        $user->save();

        return $user->fresh('memberships');
    }
}
