<?php

namespace App\Notifications;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Event $event
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->event;

        return (new MailMessage)
            ->subject('Reminder: ' . $event->name . ' is coming up!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We’re excited to remind you about an upcoming event you’re attending.')
            ->line('**Event:** ' . $event->name)
            ->line('**Description:** ' . $event->description)
            ->line('**Starts:** ' . Carbon::parse($event->start_date)->format('F j, Y g:i A'))
            ->line('**Ends:** ' . Carbon::parse($event->end_date)->format('F j, Y g:i A'))
            ->line('Organized by: ' . $event->user->name)
            ->action('View Event Details', route('events.show', $event->id))
            ->line('We look forward to seeing you there!')
            ->salutation('Best regards, The Event Management Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_name' => $this->event->name,
            'event_start_date' => $this->event->start_date,
        ];
    }
}