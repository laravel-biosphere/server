<?php

namespace Anafro\Biosphere\Channels;

use Illuminate\Support\Str;

class ChannelRegistrar
{
    private array $channels;

    public function __construct()
    {
        $this->channels = [];
    }

    public function register(string $channelPattern, string $class): void
    {
        $this->channels[$channelPattern] = $class;
    }

    public function find(string $channelName): ?Channel
    {
        foreach ($this->channels as $channelPattern => $channel) {
            if (Str::of($channelName)->test($channelPattern)) {
                return new $channel(pattern: $channelPattern, name: $channelName);
            }
        }

        return null;
    }
}
