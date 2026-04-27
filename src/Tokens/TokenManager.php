<?php

namespace Anafro\Biosphere\Tokens;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

final class TokenManager
{
    public function __construct()
    {
        //
    }

    /**
    * @param \App\Models\User|int $user
    */
    public function issueFor(mixed $user): string
    {
        $userId = is_a($user, 'App\Models\User') ? $user->id : $user;
        $token = $this->generate();

        Redis::connection()->client()->rawCommand('SET', "biosphere:token:$token", $userId, 'EX', '30');
        return $token;
    }

    private function generate(): string
    {
        return Str::uuid();
    }
}
