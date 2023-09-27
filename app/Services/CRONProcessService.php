<?php

namespace App\Services;

use App\Models\CRONProcess;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

class CRONProcessService
{
    public function getCronProcesses(): Collection
    {
        return CRONProcess::latest('last_executed_date')->get();
    }

    public function createProcess(string $command, string $title, string $period): void
    {
        if (!Schema::hasTable('cron_processes')) {
            return;
        }

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
        ]);
    }

    public function successProcess(string $command): void
    {
        $process = CRONProcess::where('command', $command)->first();

        if (!$process) {
            return;
        }

        $process->update([
            'last_executed_date' => Carbon::now(),
            'status_id' => Status::STATUS_ACTIVE
        ]);
    }

    public function failedProcess(string $command, string $errorMessage): void
    {
        $process = CRONProcess::where('command', $command)->first();

        if (!$process) {
            return;
        }
        $process->update([
            'last_executed_date' => Carbon::now(),
            'last_error' => $errorMessage,
            'status_id' => Status::STATUS_BLOCKED
        ]);

    }
}
