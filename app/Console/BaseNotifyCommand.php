<?php

namespace App\Console;

use App\Notifications\SchedulerExecuted;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Psr\Log\LoggerInterface;

class BaseNotifyCommand extends Command
{
    public string $commandName;
    private string $botId;
    private LoggerInterface $logChannel;

    public function __construct()
    {
        parent::__construct();
        $this->logChannel = Log::channel('custom_imports_log');
        $this->botId = (string) config('services.telegram-bot-api.channel_id_for_scheduler');
    }

    public function sendErrorNotification(string $message): void
    {
        $this->logChannel->debug('[ERROR] ' . $message);
        Notification::send([$this->botId], new SchedulerExecuted($this->commandName, '🛑', $message));
    }

    public function sendSuccessNotification(string $message): void
    {
        $this->logChannel->debug('[SUCCESS] ' . $message);
        Notification::send([$this->botId], new SchedulerExecuted($this->commandName, '✅', $message));
    }
}