<?php

namespace App\Console;

use App\Notifications\SchedulerExecuted;
use App\Services\CRONProcessService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Psr\Log\LoggerInterface;

class HandledCommand extends Command
{
    protected string $period;
    protected bool $needNotifyErrors;

    private string $botId;
    private array $errors;
    private LoggerInterface $logChannel;
    private CRONProcessService $CRONProcessService;

    public function __construct()
    {
        parent::__construct();

//        $logFilename = str_replace(':', '_', $this->signature);
//        $logFilename = str_replace('-', '_', $logFilename);
//        $logFilename = str_replace(' ', '_', $logFilename);
//
//        if (! array_key_exists($logFilename, config('logging.channels', []))) {
//
//            config()->set('logging.channels', array_merge(config('logging.channels', []), [
//                $logFilename => [
//                    'driver' => 'daily',
//                    'path' => storage_path('logs/imports/' . $logFilename . '.log'),
//                    'level' => 'debug'
//                ]
//            ]));
//
//            Artisan::call('optimize');
//        }
//
//        $this->logChannel = Log::channel($this->signature);

        $this->needNotifyErrors = true;
        $this->logChannel = Log::channel('custom_imports_log');
        $this->botId = (string) config('services.telegram-bot-api.channel_id_for_scheduler');
        $this->CRONProcessService = new CRONProcessService();
        $this->errors = [];
    }

    protected function isProcessRunning(): bool
    {
        $this->CRONProcessService->handleProcess(
            $this->signature,
            $this->description,
            $this->period
        );

        if ($this->CRONProcessService->isProcessFrozen($this->signature)) {
            $this->CRONProcessService->unfreezingProcess($this->signature);

            return false;
        }

        return $this->CRONProcessService->isProcessRunning($this->signature);
    }

    protected function startProcess(): void
    {
        $this->prepareStart();

        $this->logChannel->debug('------------------------START-----------------------------');
        $this->logChannel->debug('[DATETIME] ' . now()->format('d.m.Y H:i:s'));
        $this->logChannel->debug('[COMMAND] ' . $this->signature);
        $this->logChannel->debug('[DESCRIPTION] ' . $this->description);
    }

    protected function endProcess(): void
    {
        $this->prepareEnd();

        $this->logChannel->debug('[DATETIME] ' . now()->format('d.m.Y H:i:s'));
        $this->logChannel->debug('------------------------END-------------------------------');
    }

    private function prepareStart(): void
    {
        $this->CRONProcessService->runProcess($this->signature);
    }

    private function prepareEnd(): void
    {
        if (count($this->errors) === 0) {
            $this->CRONProcessService->successProcess($this->signature);
            return;
        }

        $errorMessage = implode("\n\n", $this->errors);

        $this->CRONProcessService->failedProcess($this->signature, $errorMessage);

        if ($this->needNotifyErrors) {
            Notification::send([$this->botId], new SchedulerExecuted($this->signature, '🛑', $errorMessage));
        }
    }

    protected function sendInfoMessage(string $message): void
    {
        $this->logChannel->debug('[INFO] ' . $message);
    }

    protected function sendErrorMessage(string $message): void
    {
        $this->errors[] = $message;
        $this->logChannel->debug('[ERROR] ' . $message);
    }
}