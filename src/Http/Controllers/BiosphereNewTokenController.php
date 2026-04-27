<?php

namespace Anafro\Biosphere\Http\Controllers;

use Anafro\Biosphere\Tokens\TokenManager;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\JsonResponse;

class BiosphereNewTokenController
{
    public function __invoke(#[CurrentUser] User $user): JsonResponse
    {
        $tokens = resolve(TokenManager::class);
        $token = $tokens->issueFor($user);

        return response()->json(compact('token'));
    }
}
