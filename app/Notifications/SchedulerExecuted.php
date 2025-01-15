<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class SchedulerExecuted extends Notification
{
    private string $commandName;
    private string $message;
    private string $status;

    public function __construct(string $commandName, string $status, string $message)
    {
        $this->commandName = $commandName;
        $this->status = $status;
        $this->message = $message;
    }

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        $title = "<strong>{$this->status} Планировщик задач</strong>\n\n";
        $commandName = "<strong>{$this->commandName}</strong>\n\n";
        $message = mb_substr($this->message, 0, 500);

        $content = "{$title}{$commandName}{$message}";

        return TelegramMessage::create($content)
            ->options(['parse_mode' => 'html'])
            ->to($notifiable);
    }
}
