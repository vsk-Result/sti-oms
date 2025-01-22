<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\ScheduleExport;
use App\Services\ScheduleExportService;
use Exception;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleExportTasksRunner extends HandledCommand
{
    protected $signature = 'oms:schedule-export-tasks-runner';

    protected $description = 'Обрабатывает задачи планировщика формирования отчетов и отправляет результат на почту пользователя';

    protected string $period = 'Каждую минуту';

    public function __construct(private ScheduleExportService $scheduleExportService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if (! $this->scheduleExportService->hasTasksToRun()) {
            return 0;
        }

        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $taskToRun = $this->scheduleExportService->getTaskToRun();

        $taskToRun->update([
            'status_id' => ScheduleExport::STATUS_IN_PROGRESS
        ]);

        try {
            $this->sendInfoMessage('Формирование отчета "' . $taskToRun->name . '"');

            $data = (array) json_decode($taskToRun->data);

            $filename = $taskToRun->filepath . '/' . $taskToRun->id . '___' . $taskToRun->filename;

            Excel::store(new $taskToRun->model($data), $filename);
        } catch(Exception $e){
            $taskToRun->update([
                'status_id' => ScheduleExport::STATUS_CANCELED
            ]);

            $this->sendErrorMessage('Не удалось сформировать отчет: "' . $taskToRun->name . ' - ' . $e->getMessage());
            $this->endProcess();

            return 0;
        }

        $taskToRun->update([
            'status_id' => ScheduleExport::STATUS_DONE
        ]);

        $this->sendInfoMessage('Отчет "' . $taskToRun->name . '" успешно сформирован');

        try {
            $exportName = $taskToRun->name;
            $email = $taskToRun->send_to_email;
            $filepath = storage_path('') . '/app/public/' . $filename;

            $this->sendInfoMessage('Отправка отчета "' . $taskToRun->name . '" на email "' . $email . '"');

            Mail::send('emails.schedule-exports', compact('exportName', ), function ($m) use ($exportName, $filepath, $email) {
                $m->from('support@st-ing.com', 'OMS Отчет сформирован');
                $m->subject($exportName);
                $m->attach($filepath);

                $m->to($email);
            });
        } catch(Exception $e){
            $this->sendErrorMessage('Не удалось отправить отчет "' . $taskToRun->name . '" email "' . $email . '": ' . $e->getMessage());
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Отчет "' . $taskToRun->name . '" успешно отправлен на email "' . $email . '"');

        $this->endProcess();

        return 0;
    }
}
