<?php

namespace App\Notifications;

use App\Channels\BotChannel;
use App\Channels\Messages\BotMessage;
use App\Channels\Messages\DiscordMessage;
use App\Channels\WebhookChannel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DivisionEdited extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private $division, private $user)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [BotChannel::class];
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function toWebhook($notifiable)
    {
        $channel = $notifiable->settings()->get('officer_channel');

        $authoringUser = auth()->check() ? auth()->user()->name : 'ClanAOD';

        return (new DiscordMessage())
            ->to($channel)
            ->message(":tools: **{$authoringUser}** updated division settings for **{$notifiable->name}**")
            ->success()
            ->send();
    }

    public function toBot($notifiable)
    {
        return (new BotMessage())
            ->title($notifiable->name.' Division')
            ->thumbnail(getDivisionIconPath($notifiable->abbreviation))
            ->message(sprintf('%s updated the division settings', $this->user))
            ->info()
            ->send();
    }
}
