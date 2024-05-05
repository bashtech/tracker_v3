<?php

namespace App\Channels\Messages;

use Exception;

class BotReactMessage
{
    public $states = [
        'resolved' => '✅',
        'rejected' => '❌',
        'assigned' => '⏳',
        'in_progress' => '⏳',
        'on_hold' => '⚠️',
    ];

    private string $emote;

    private string $messageId;

    private $target;

    public function __construct(private $notifiable)
    {
    }

    public function to(string $channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return $this
     */
    public function target($target)
    {
        $this->target = $target;

        return $this;
    }

    public function status(string $status)
    {
        if (! isset($this->states[$status])) {
            throw new \Exception('Invalid status provided to BotReactMessage');
        }

        $this->emote = $this->states[$status];

        return $this;
    }

    /**
     * @throws Exception
     */
    public function send(): array
    {
        if (! $this->emote) {
            throw new Exception('A status {assigned, in_progress, on_hold, resolved, rejected} must be defined');
        }

        $routeTarget = $this->notifiable->routeNotificationFor('bot');
        if (! isset($routeTarget)) {
            throw new Exception('A channel target must be defined');
        }

        return [
            'api_uri' => sprintf('channels/%s/messages/%s/react', $routeTarget, $this->notifiable->message_id),
            'body' => [
                'emoji' => $this->emote,
                'exclusive' => true,
            ],
        ];
    }
}
