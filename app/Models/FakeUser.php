<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;

class FakeUser implements JWTSubject
{
    protected $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    public function getJWTIdentifier()
    {
        return $this->username;
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
