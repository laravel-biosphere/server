<?php

namespace Anafro\Biosphere\Messages;

use Anafro\Biosphere\Channels\Channel;
use Illuminate\Foundation\Auth\User;

readonly class Message
{
    public function __construct(
        public string $receiver,
        public string $event,
        public Channel $channel,
        public array $data,
        public ?User $user = null,
    ) {
        //
    }

    public function toJson(?string $receiver = null, int $options = 0): string
    {
        return json_encode(value: [
            "receiver" => $receiver ?? $this->receiver,
            "event" => $this->event,
            "channel" => $this->channel->name,
            ...$this->data,
        ], flags: $options);
    }
}
