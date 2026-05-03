<?php

namespace Anafro\Biosphere\Facades;

use Anafro\Biosphere\Channels\ChannelRegistrar;
use Anafro\Biosphere\Http\Controllers\BiosphereAuthorizationController;
use Anafro\Biosphere\Http\Controllers\BiosphereNewTokenController;
use Anafro\Biosphere\Messages\Message;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

final class Biosphere
{
    private function __construct()
    {
        //
    }

    public static function routes()
    {
        Route::middleware('biosphere.proxy')->group(function () {
            Route::post('/biosphere/authorize', BiosphereAuthorizationController::class);
        });

        Route::post('/biosphere/new-token', BiosphereNewTokenController::class);
    }

    public static function channel(string $pattern, string $class)
    {
        $channels = resolve(ChannelRegistrar::class);
        $channels->register($pattern, $class);
    }

    public static function send(Message $message)
    {
        $redisChannel = env('BIOSPHERE_REDIS_CHANNEL_FROM_SERVER');
        Redis::connection('pub')->publish($redisChannel, $message->toJson(receiver: 'client'));
    }
}
