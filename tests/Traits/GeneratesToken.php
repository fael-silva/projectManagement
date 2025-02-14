<?php

namespace Tests\Traits;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait GeneratesToken
{
    /**
     *
     * @param \App\Models\User|null $user
     * @return string
     */
    public function generateToken(User $user = null)
    {
        $user = $user ?? User::factory()->create();
        return JWTAuth::fromUser($user);
    }
}
