<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;
use Throwable;

class ErrorOccurred extends Notification
{
    private Throwable $error;

    public function __construct(Throwable $e)
    {
        $this->error = $e;
    }


    public function via()
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->to($notifiable)
            ->content("Пользователь: " . auth()->user()->name . "\nURL: " . request()->fullUrl() . "\nОшибка: " . substr($this->error->getMessage(), 0, 300) . '...');
    }
}
