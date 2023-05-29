<?php

namespace App\Console\Commands;

use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\ObjectService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateGeneralCosts extends Command
{
    protected $signature = 'oms-imports:update-general-costs';

    protected $description = 'Обновляет общие затраты объектов';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Обновляет общие затраты объектов');

        $object27_1 = BObject::where('code', '27.1')->first();
        $object27_8 = BObject::where('code', '27.8')->first();
        $general2017 = Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount') + Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2018 = 21421114 + Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount') + Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2019 = 39760000 + 692048 + Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount') + Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2020 = 2000000 + 418000 + 1615000 + Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount') + Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2021_1 = 600000 + Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount') + Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('object_id', $object27_1->id)->sum('amount') + (Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2021_2 = 600000 + 68689966 + Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount') + Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2022_1 = Payment::whereBetween('date', ['2022-01-01', '2022-10-11'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount') + Payment::whereBetween('date', ['2022-01-01', '2022-10-11'])->where('object_id', $object27_1->id)->sum('amount') + (Payment::whereBetween('date', ['2022-01-01', '2022-10-11'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2022_2 = Payment::whereBetween('date', ['2022-10-12', '2022-12-31'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount') + Payment::whereBetween('date', ['2022-10-12', '2022-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (Payment::whereBetween('date', ['2022-10-12', '2022-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2023 = Payment::whereBetween('date', ['2023-01-01', '2023-12-31'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount') + Payment::whereBetween('date', ['2023-01-01', '2023-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (Payment::whereBetween('date', ['2023-01-01', '2023-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);

        $generalTotal = $general2017 + $general2018 + $general2019 + $general2020 + $general2021_1 + $general2021_2 + $general2022_1 + $general2022_2 + $general2023;
        $info2017 = ObjectService::getGeneralCostsByPeriod('2017-01-01', '2017-12-31');
        $info2018 = ObjectService::getGeneralCostsByPeriod('2018-01-01', '2018-12-31', 21421114);
        $info2019 = ObjectService::getGeneralCostsByPeriod('2019-01-01', '2019-12-31', (39760000 + 692048));
        $info2020 = ObjectService::getGeneralCostsByPeriod('2020-01-01', '2020-12-31', (2000000 + 418000 + 1615000));
        $info2021_1 = ObjectService::getGeneralCostsByPeriod('2021-01-01', '2021-03-02', 600000);
        $info2021_2 = ObjectService::getGeneralCostsByPeriod('2021-03-03', '2021-12-31', (600000 + 68689966));
        $info2022_1 = ObjectService::getGeneralCostsByPeriod('2022-01-01', '2022-10-11');
        $info2022_2 = ObjectService::getGeneralCostsByPeriod('2022-10-12', '2022-12-31');
        $info2023 = ObjectService::getGeneralCostsByPeriod('2023-01-01', '2023-12-31');

        Log::channel('custom_imports_log')->debug('[SUCCESS] Данные успешно обновлены');

        return 0;
    }
}
