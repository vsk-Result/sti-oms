<?php

namespace App\Services;

use App\Models\ScheduleExport;

class ScheduleExportService
{
    public function isTaskInProgress(string $name, array $data): bool
    {
        $dataHash = md5(json_encode($data));
        $task = ScheduleExport::where('data_hash', $dataHash)
            ->where('name', $name)
            ->where('created_by_user_id', auth()->id())
            ->first();

        if (! $task) {
            return false;
        }

        return $task->isInProgress();
    }

    public function isTaskReady(string $name, array $data): bool
    {
        $dataHash = md5(json_encode($data));
        $task = ScheduleExport::where('data_hash', $dataHash)
            ->where('name', $name)
            ->where('created_by_user_id', auth()->id())
            ->first();

        if (! $task) {
            return false;
        }

        return $task->isReady();
    }

    public function hasTasksToRun(): bool
    {
        return ScheduleExport::ready()->count() > 0;
    }

    public function getTaskToRun(): ScheduleExport | null
    {
        return ScheduleExport::ready()->orderBy('id')->first();
    }

    public function createTask(string $name, string $model, string $filepath, string $filename, array $data, string $email): void
    {
        ScheduleExport::create([
            'name' => $name,
            'model' => $model,
            'filepath' => $filepath,
            'filename' => $filename,
            'data' => json_encode($data),
            'send_to_email' => $email,
            'data_hash' => md5(json_encode($data)),
            'status_id' => ScheduleExport::STATUS_READY
        ]);
    }
}