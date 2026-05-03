<?php

namespace Anafro\Biosphere\Commands;

use Anafro\Biosphere\Channels\ChannelRegistrar;
use Anafro\Biosphere\Messages\Message;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Console\Command;
use Throwable;

class BiosphereServe extends Command
{
    protected $model = 'App\\Models\\User';
    protected $signature = 'biosphere:serve';

    public function handle()
    {
        $redisChannel = env('BIOSPHERE_REDIS_CHANNEL_TO_SERVER');
        Redis::connection('sub')->subscribe([$redisChannel], Closure::fromCallable([$this, 'onMessage']));
    }

    public function onMessage(string $message): void
    {
        try {
            $message = json_decode($message, associative: true);
            $channelName = $message['channel'];
            $event = $message['event'];
            $user = $this->model::find($message['userId']);

            $channels = resolve(ChannelRegistrar::class);
            $channel = $channels->find($channelName);

            if ($channel === null) {
                Log::warning("A connecting channel $channelName does not match any channel patterns.");
                return;
            }

            $message = new Message(
                receiver: 'server',
                event: $event,
                channel: $channel,
                user: $user,
                data: collect($message)->except(['event', 'channel', 'userId'])->toArray(),
            );

            match ($message->event) {
                'connect' =>    $channel->connected($user),
                'disconnect' => $channel->disconnected($user),
                'ponged' =>     $channel->heartbeat($user),
                default =>      $channel->message($message),
            };
        } catch (Throwable $e) {
            Log::error($e);
        }
    }
}
