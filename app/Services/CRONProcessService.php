<?php

namespace App\Services;

use App\Models\CRONProcess;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

class CRONProcessService
{
    const FREEZE_TIME_LIMIT_MINUTES = 45;

    public function getCronProcesses(): Collection
    {
        return CRONProcess::orderBy('status_id')->get();
    }

    public function isProcessFrozen(string $command): bool
    {
        $process = CRONProcess::where('command', $command)->first();

        return $process->isRunning() && ($this->isOverLimit($process->last_running_date) || $this->isOverLimit($process->last_executed_date));
    }

    public function isProcessRunning(string $command): bool
    {
        $process = CRONProcess::where('command', $command)->first();

        return $process && $process->isRunning();
    }

    public function handleProcess(string $command, string $title, string $period)
    {
        $process = CRONProcess::where('command', $command)->first();

        if ($process) {
            $process->update([
                'title' => $title,
                'period' => $period,
            ]);

            return;
        }

        CRONProcess::create([
            'command' => $command,
            'title' => $title,
            'period' => $period,
            'status_id' => CRONProcess::STATUS_NEW
        ]);
    }

    public function runProcess(string $command): void
    {
        CRONProcess::where('command', $command)->update([
            'last_error' => '',
            'last_running_date' => now(),
            'status_id' => CRONProcess::STATUS_RUNNING,
        ]);
    }

    public function successProcess(string $command): void
    {
        CRONProcess::where('command', $command)->update([
            'last_executed_date' => now(),
            'status_id' => CRONProcess::STATUS_SUCCESS
        ]);
    }

    public function unfreezingProcess(string $command): void
    {
        CRONProcess::where('command', $command)->update([
            'last_error' => '',
            'last_executed_date' => null,
            'last_running_date' => null,
            'status_id' => CRONProcess::STATUS_NEW
        ]);
    }

    public function failedProcess(string $command, string $errorMessage): void
    {
        CRONProcess::where('command', $command)->update([
            'last_executed_date' => now(),
            'last_error' => $errorMessage,
            'status_id' => CRONProcess::STATUS_ERROR
        ]);
    }

    private function isOverLimit($date): bool
    {
        return now()->diffInMinutes(Carbon::parse($date)) >= self::FREEZE_TIME_LIMIT_MINUTES;
    }
}
