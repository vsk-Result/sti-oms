<?php

namespace App\Console\Commands;

use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\CRONProcessService;
use App\Services\ObjectService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateGeneralCosts extends Command
{
    protected $signature = 'oms:update-general-costs';

    protected $description = 'Обновляет общие затраты объектов';

    public function __construct(CRONProcessService $CRONProcessService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно в 19:00'
        );
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Обновляет общие затраты объектов');

        try {
            $periods = [
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-12-31',
                    'bonus' => 0,
                ],
                [
                    'start_date' => '2018-01-01',
                    'end_date' => '2018-12-31',
                    'bonus' => 21421114,
                ],
                [
                    'start_date' => '2019-01-01',
                    'end_date' => '2019-12-31',
                    'bonus' => (39760000 + 692048),
                ],
                [
                    'start_date' => '2020-01-01',
                    'end_date' => '2020-12-31',
                    'bonus' => (2000000 + 418000 + 1615000),
                ],
                [
                    'start_date' => '2021-01-01',
                    'end_date' => '2021-03-02',
                    'bonus' => 600000,
                ],
                [
                    'start_date' => '2021-03-03',
                    'end_date' => '2021-12-31',
                    'bonus' => (600000 + 68689966),
                ],
                [
                    'start_date' => '2022-01-01',
                    'end_date' => '2022-10-11',
                    'bonus' => 0,
                ],
                [
                    'start_date' => '2022-10-12',
                    'end_date' => '2022-12-31',
                    'bonus' => 0,
                ],
                [
                    'start_date' => '2023-01-01',
                    'end_date' => '2023-07-20',
                    'bonus' => 0,
                ],
                [
                    'start_date' => '2023-07-21',
                    'end_date' => '2023-12-31',
                    'bonus' => 0,
                ]
            ];


            $object27_1 = BObject::where('code', '27.1')->first();
            $object27_8 = BObject::where('code', '27.8')->first();



            $periods = array_reverse($periods);

            $generalTotalAmount = 0;
            $generalInfo = [];
            foreach ($periods as $index => $period) {
                $datesBetween = [$period['start_date'], $period['end_date']];
                $paymentQuery = \App\Models\Payment::query()->whereBetween('date', $datesBetween)->where('company_id', 1);
                $generalAmount = (clone $paymentQuery)->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount')
                    + (clone $paymentQuery)->where('object_id', $object27_1->id)->sum('amount')
                    + ((clone $paymentQuery)->where('object_id', $object27_8->id)->sum('amount') * 0.7)
                    + $period['bonus'];

                $generalInfo[$index] = [
                    'start_date' => $period['start_date'],
                    'end_date' => $period['end_date'],
                    'general_amount' => $generalAmount,
                    'info' => \App\Services\ObjectService::getGeneralCostsByPeriod($period['start_date'], $period['end_date'], $period['bonus']),
                ];

                $generalTotalAmount += $generalAmount;
            }
        } catch (\Exception $e) {
            $errorMessage = '[ERROR] Ошибка в вычислениях: ' . $e->getMessage();
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
            return 0;
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Данные успешно обновлены');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
