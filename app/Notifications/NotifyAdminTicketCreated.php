<?php

namespace App\Notifications;

use App\Channels\Messages\DiscordMessage;
use App\Channels\WebhookChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NotifyAdminTicketCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via()
    {
        return [WebhookChannel::class];
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function toWebhook($ticket)
    {
        $channel = config('app.aod.admin-ticketing-channel');

        $authoringUser = $ticket->caller ? $ticket->caller->name : 'UNK';

        return (new DiscordMessage())
            ->to($channel)
            ->messageId($ticket->message_id)
            ->info()
            ->fields([
                [
                    'name' => "Type: {$ticket->type->name}",
                    'value' => "Submitted by {$authoringUser}",
                ], [
                    'name' => 'Link to ticket',
                    'value' => route('help.tickets.show', $ticket),
                ],
            ])->send();
    }
}
