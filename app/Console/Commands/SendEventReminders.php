<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending notifictation to all event participants that event start in 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = Event::with('atendees.user')->whereBetween('start_date', [now(), now()->addDay()])->get();

        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);

        $events->each(function ($event) {
            $event->atendees->each(function ($atendee) use ($event) {
                $this->info("Sending reminder to {$atendee->user->name} for {$event->name}");
            });
        });

        $this->info("Sending event reminders for {$eventCount} {$eventLabel}");
    }
}