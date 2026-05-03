<?php

namespace Anafro\Biosphere\Channels;

use Anafro\Biosphere\Facades\Biosphere;
use Anafro\Biosphere\Messages\Message;
use Illuminate\Support\Facades\Log;

abstract class Channel
{
    protected array $parameters;

    public function __construct(
        protected string $pattern,
        public readonly string $name,
    ) {
        $this->parameters = [];
        preg_match(pattern: $this->pattern, subject: $this->name, matches: $this->parameters);
    }

    public function parameter(string $name): string
    {
        return $this->parameters[$name];
    }

    public function send(string $event, array $data = [])
    {
        $message = new Message(
            receiver: 'client',
            event: $event,
            channel: $this,
            data: $data,
        );
        Biosphere::send($message);
    }

    public function schedule(string $key, string $event, string $delay, array $data)
    {
        $this->send($event, [
            'schedule' => $key,
            ...compact('delay'),
            ...$data,
        ]);

        Log::info("(Server) Schedule $key!");
    }

    public function cancel(string $key)
    {
        $this->send('cancel', [
            'cancel' => $key,
        ]);

        Log::info("(Server) Cancel $key!");
    }

    /**
     * @param \App\Models\User $user
     */
    abstract public function authorize(mixed $user): bool;
    /**
     * @param \App\Models\User $user
     */
    abstract public function connected(mixed $user): void;

    /**
     * @param \App\Models\User $user
     */
    abstract public function heartbeat(mixed $user): void;

    /**
     * @param \App\Models\User $user
     */
    abstract public function disconnected(mixed $user): void;
    abstract public function message(Message $message): void;
}
