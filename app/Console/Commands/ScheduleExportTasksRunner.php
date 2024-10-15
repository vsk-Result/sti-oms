<?php

namespace App\Console\Commands;

use App\Models\ScheduleExport;
use App\Services\CRONProcessService;
use App\Services\ScheduleExportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleExportTasksRunner extends Command
{
    protected $signature = 'oms:schedule-export-tasks-runner';

    protected $description = 'Обрабатывает задачи планировщика формирования отчетов и отправляет результат на почту пользователя';

    private ScheduleExportService $scheduleExportService;

    public function __construct(CRONProcessService $CRONProcessService, ScheduleExportService $scheduleExportService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Каждую минуту'
        );
        $this->scheduleExportService = $scheduleExportService;
    }

    public function handle()
    {
        if (! $this->scheduleExportService->hasTasksToRun()) {
            return 0;
        }

        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Запуск обработки задач планировщика формирования отчетов');

        $taskToRun = $this->scheduleExportService->getTaskToRun();

        $taskToRun->update([
            'status_id' => ScheduleExport::STATUS_IN_PROGRESS
        ]);

        try {
            Log::channel('custom_imports_log')->debug('[INFO] Формирование отчета "' . $taskToRun->name . '"');

            $data = (array) json_decode($taskToRun->data);

            $filename = $taskToRun->filepath . '/' . $taskToRun->id . '___' . $taskToRun->filename;

            Excel::store(new $taskToRun->model($data), $filename);
        } catch(Exception $e){
            $taskToRun->update([
                'status_id' => ScheduleExport::STATUS_CANCELED
            ]);

            $errorMessage = '[ERROR] Не удалось сформировать отчет: "' . $taskToRun->name . ' - ' . $e->getMessage();

            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);

            return 0;
        }

        $taskToRun->update([
            'status_id' => ScheduleExport::STATUS_DONE
        ]);

        Log::channel('custom_imports_log')->debug('[INFO] Отчет "' . $taskToRun->name . '" успешно сформирован');

        try {
            $exportName = $taskToRun->name;
            $email = $taskToRun->createdBy->email;
            $filepath = storage_path('') . '/app/public/' . $filename;

            Log::channel('custom_imports_log')->debug('[INFO] Отправка отчета "' . $taskToRun->name . '" на email "' . $email . '"');

            Mail::send('emails.schedule-exports', [], function ($m) use ($exportName, $filepath, $email) {
                $m->from('support@st-ing.com', 'OMS Отчет "' . $exportName . '" сформирован');
                $m->subject('OMS');
                $m->attach($filepath);

                $m->to($email);
            });
        } catch(Exception $e){
            $errorMessage = '[ERROR] Не удалось отправить отчет "' . $taskToRun->name . '" email "' . $email . '": ' . $e->getMessage();

            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);

            return 0;
        }

        $this->CRONProcessService->successProcess($this->signature);
        Log::channel('custom_imports_log')->debug('[SUCCESS] Отчет "' . $taskToRun->name . '" успешно отправлен на email "' . $email . '"');


        return 0;
    }
}
