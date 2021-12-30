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

    public function via(): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->to($notifiable)
            ->content(
                sprintf(
                    "*Пользователь:* %s\n---\n*Пред. URL:* %s\n*URL:* %s (%s)\n---\n*Файл:* %s (%s)\n---\n*Ошибка:* %s...",
                    auth()->user()->name,
                    url()->previous(),
                    request()->fullUrl(),
                    request()->method(),
                    $this->error->getFile(),
                    $this->error->getLine(),
                    mb_substr($this->error->getMessage(), 0, 300, 'UTF-8')
                )
            );
    }
}
