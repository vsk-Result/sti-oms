<?php

namespace App\Exports\Finance\GeneralCosts\Sheets;

use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use App\Models\Payment;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles,
    WithEvents
{
    private string $sheetName;

    public function __construct(string $sheetName)
    {
        $this->sheetName = $sheetName;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $codes = GeneralCost::getObjectCodesForGeneralCosts();
        $objects = BObject::whereIn('code', $codes)->orderByDesc('code')->with(['customers', 'payments' => function($q) {
            $q->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->where('amount', '>=', 0);
        }])->get();

        $object27_1 = \App\Models\Object\BObject::where('code', '27.1')->first();
        $object27_8 = \App\Models\Object\BObject::where('code', '27.8')->first();
        $general2017 = \App\Models\Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2018 = 21421114 + \App\Models\Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2019 = 39760000 + 692048 + \App\Models\Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2020 = 2000000 + 418000 + 1615000 + \App\Models\Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);

        $general2021_1 = 600000 + \App\Models\Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2021_2 = 600000 + 68689966 + \App\Models\Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);

        $general2022_1 = \App\Models\Payment::whereBetween('date', ['2022-01-01', '2022-10-11'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2022-01-01', '2022-10-11'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2022-01-01', '2022-10-11'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2022_2 = \App\Models\Payment::whereBetween('date', ['2022-10-12', '2022-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2022-10-12', '2022-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2022-10-12', '2022-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);

        $general2023_1 = \App\Models\Payment::whereBetween('date', ['2023-01-01', '2023-07-20'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2023-01-01', '2023-07-20'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2023-01-01', '2023-07-20'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2023_2 = \App\Models\Payment::whereBetween('date', ['2023-07-21', '2023-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2023-07-21', '2023-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2023-07-21', '2023-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);

        $generalTotal = $general2017 + $general2018 + $general2019 + $general2020 + $general2021_1 + $general2021_2 + $general2022_1 + $general2022_2 + $general2023_1 + $general2023_2;

        $info2017 = \App\Services\ObjectService::getGeneralCostsByPeriod('2017-01-01', '2017-12-31');
        $info2018 = \App\Services\ObjectService::getGeneralCostsByPeriod('2018-01-01', '2018-12-31', 21421114);
        $info2019 = \App\Services\ObjectService::getGeneralCostsByPeriod('2019-01-01', '2019-12-31', (39760000 + 692048));
        $info2020 = \App\Services\ObjectService::getGeneralCostsByPeriod('2020-01-01', '2020-12-31', (2000000 + 418000 + 1615000));
        $info2021_1 = \App\Services\ObjectService::getGeneralCostsByPeriod('2021-01-01', '2021-03-02', 600000);
        $info2021_2 = \App\Services\ObjectService::getGeneralCostsByPeriod('2021-03-03', '2021-12-31', (600000 + 68689966));
        $info2022_1 = \App\Services\ObjectService::getGeneralCostsByPeriod('2022-01-01', '2022-10-11');
        $info2022_2 = \App\Services\ObjectService::getGeneralCostsByPeriod('2022-10-12', '2022-12-31');
        $info2023_1 = \App\Services\ObjectService::getGeneralCostsByPeriod('2023-01-01', '2023-07-20');
        $info2023_2 = \App\Services\ObjectService::getGeneralCostsByPeriod('2023-07-21', '2023-12-31');

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'РАЗБИВКА ОБЩИХ ЗАТРАТ ПО ГОДАМ');
        $sheet->setCellValue('B1', 'ИТОГО');
        $sheet->setCellValue('D1', CurrencyExchangeRate::format($generalTotal, 'RUB', 0, true));
        $sheet->setCellValue('E1', 'С 31.12.23 ПО 21.07.23');
        $sheet->setCellValue('F1', CurrencyExchangeRate::format($general2023_2, 'RUB', 0, true));
        $sheet->setCellValue('G1', 'С 20.07.23 ПО 01.01.23');
        $sheet->setCellValue('H1', CurrencyExchangeRate::format($general2023_1, 'RUB', 0, true));
        $sheet->setCellValue('I1', 'С 31.12.22 ПО 12.10.22');
        $sheet->setCellValue('J1', CurrencyExchangeRate::format($general2022_2, 'RUB', 0, true));
        $sheet->setCellValue('K1', 'С 11.10.22 ПО 01.01.2022');
        $sheet->setCellValue('L1', CurrencyExchangeRate::format($general2022_1, 'RUB', 0, true));
        $sheet->setCellValue('M1', 'С 31.12.21 ПО 03.03.21');
        $sheet->setCellValue('N1', CurrencyExchangeRate::format($general2021_2, 'RUB', 0, true));
        $sheet->setCellValue('O1', 'С 02.03.21 ПО 01.01.2021');
        $sheet->setCellValue('O1', CurrencyExchangeRate::format($general2021_1, 'RUB', 0, true));
        $sheet->setCellValue('Q1', '2020');
        $sheet->setCellValue('R1', CurrencyExchangeRate::format($general2020, 'RUB', 0, true));
        $sheet->setCellValue('S1', '2019');
        $sheet->setCellValue('T1', CurrencyExchangeRate::format($general2019, 'RUB', 0, true));
        $sheet->setCellValue('U1', '2018');
        $sheet->setCellValue('V1', CurrencyExchangeRate::format($general2018, 'RUB', 0, true));
        $sheet->setCellValue('W1', '2017');
        $sheet->setCellValue('X1', CurrencyExchangeRate::format($general2017, 'RUB', 0, true));

        $sheet->getStyle('D1')->getFont()->setColor(new Color($generalTotal < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('F1')->getFont()->setColor(new Color($general2023_2 < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('H1')->getFont()->setColor(new Color($general2023_1 < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('J1')->getFont()->setColor(new Color($general2022_2 < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('L1')->getFont()->setColor(new Color($general2022_1 < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('N1')->getFont()->setColor(new Color($general2021_2 < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('P1')->getFont()->setColor(new Color($general2021_1 < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('R1')->getFont()->setColor(new Color($general2020 < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('T1')->getFont()->setColor(new Color($general2019 < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('V1')->getFont()->setColor(new Color($general2018 < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('X1')->getFont()->setColor(new Color($general2017 < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));


        $sheet->setCellValue('A2', 'Объект');
        $sheet->setCellValue('B2', 'Получено');
        $sheet->setCellValue('C2', '%');
        $sheet->setCellValue('D2', 'Общие расходы');
        $sheet->setCellValue('E2', 'Получено');
        $sheet->setCellValue('F2', 'Общие расходы на объект');
        $sheet->setCellValue('G2', 'Получено');
        $sheet->setCellValue('H2', 'Общие расходы на объект');
        $sheet->setCellValue('I2', 'Получено');
        $sheet->setCellValue('J2', 'Общие расходы на объект');
        $sheet->setCellValue('K2', 'Получено');
        $sheet->setCellValue('L2', 'Общие расходы на объект');
        $sheet->setCellValue('M2', 'Получено');
        $sheet->setCellValue('N2', 'Общие расходы на объект');
        $sheet->setCellValue('O2', 'Получено');
        $sheet->setCellValue('P2', 'Общие расходы на объект');
        $sheet->setCellValue('Q2', 'Получено');
        $sheet->setCellValue('R2', 'Общие расходы на объект');
        $sheet->setCellValue('S2', 'Получено');
        $sheet->setCellValue('T2', 'Общие расходы на объект');
        $sheet->setCellValue('U2', 'Получено');
        $sheet->setCellValue('V2', 'Общие расходы на объект');
        $sheet->setCellValue('W2', 'Получено');
        $sheet->setCellValue('X2', 'Общие расходы на объект');

        $row = 3;
        foreach($objects as $object) {
            if ($object->code == 288) {
                $sheet->setCellValue('A' . $row, $object->getName() . ' | 1 (Строительство)');

                $totalCuming = ($info2017[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2018[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2019[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2020[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2021_1[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2021_2[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2022_1[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2022_2[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2023_1[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2023_2[$object->id.'|1']['cuming_amount'] ?? 0);
                $totalGeneral = ($info2017[$object->id.'|1']['general_amount'] ?? 0) + ($info2018[$object->id.'|1']['general_amount'] ?? 0) + ($info2019[$object->id.'|1']['general_amount'] ?? 0) + ($info2020[$object->id.'|1']['general_amount'] ?? 0) + ($info2021_1[$object->id.'|1']['general_amount'] ?? 0) + ($info2021_2[$object->id.'|1']['general_amount'] ?? 0) + ($info2022_1[$object->id.'|1']['general_amount'] ?? 0) + ($info2022_2[$object->id.'|1']['general_amount'] ?? 0) + ($info2023_1[$object->id.'|1']['general_amount'] ?? 0) + ($info2023_2[$object->id.'|1']['general_amount'] ?? 0);
                \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral);

                $sheet->setCellValue('B' . $row, CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true));
                $sheet->setCellValue('C' . $row, number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2));
                $sheet->setCellValue('D' . $row, CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true));

                $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($totalCuming < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($totalGeneral < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                if (isset ($info2023_2[$object->id.'|1'])) {
                    $sheet->setCellValue('E' . $row, CurrencyExchangeRate::format($info2023_2[$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('F' . $row, CurrencyExchangeRate::format($info2023_2[$object->id.'|1']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('E' . $row)->getFont()->setColor(new Color($info2023_2[$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('F' . $row)->getFont()->setColor(new Color($info2023_2[$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2023_1[$object->id.'|1'])) {
                    $sheet->setCellValue('G' . $row, CurrencyExchangeRate::format($info2023_1[$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('H' . $row, CurrencyExchangeRate::format($info2023_1[$object->id.'|1']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('G' . $row)->getFont()->setColor(new Color($info2023_1[$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('H' . $row)->getFont()->setColor(new Color($info2023_1[$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2022_2[$object->id.'|1'])) {
                    $sheet->setCellValue('I' . $row, CurrencyExchangeRate::format($info2022_2[$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('J' . $row, CurrencyExchangeRate::format($info2022_2[$object->id.'|1']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('I' . $row)->getFont()->setColor(new Color($info2022_2[$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('J' . $row)->getFont()->setColor(new Color($info2022_2[$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2022_1[$object->id.'|1'])) {
                    $sheet->setCellValue('K' . $row, CurrencyExchangeRate::format($info2022_1[$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('L' . $row, CurrencyExchangeRate::format($info2022_1[$object->id.'|1']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('K' . $row)->getFont()->setColor(new Color($info2022_1[$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('L' . $row)->getFont()->setColor(new Color($info2022_1[$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2021_2[$object->id.'|1'])) {
                    $sheet->setCellValue('M' . $row, CurrencyExchangeRate::format($info2021_2[$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('N' . $row, CurrencyExchangeRate::format($info2021_2[$object->id.'|1']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('M' . $row)->getFont()->setColor(new Color($info2021_2[$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('N' . $row)->getFont()->setColor(new Color($info2021_2[$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2021_1[$object->id.'|1'])) {
                    $sheet->setCellValue('O' . $row, CurrencyExchangeRate::format($info2021_1[$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('P' . $row, CurrencyExchangeRate::format($info2021_1[$object->id.'|1']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('O' . $row)->getFont()->setColor(new Color($info2021_1[$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('P' . $row)->getFont()->setColor(new Color($info2021_1[$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2020[$object->id.'|1'])) {
                    $sheet->setCellValue('Q' . $row, CurrencyExchangeRate::format($info2020[$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('R' . $row, CurrencyExchangeRate::format($info2020[$object->id.'|1']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('Q' . $row)->getFont()->setColor(new Color($info2020[$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('R' . $row)->getFont()->setColor(new Color($info2020[$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2019[$object->id.'|1'])) {
                    $sheet->setCellValue('S' . $row, CurrencyExchangeRate::format($info2019[$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('T' . $row, CurrencyExchangeRate::format($info2019[$object->id.'|1']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('S' . $row)->getFont()->setColor(new Color($info2019[$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('T' . $row)->getFont()->setColor(new Color($info2019[$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2018[$object->id.'|1'])) {
                    $sheet->setCellValue('U' . $row, CurrencyExchangeRate::format($info2018[$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('V' . $row, CurrencyExchangeRate::format($info2018[$object->id.'|1']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('U' . $row)->getFont()->setColor(new Color($info2018[$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('V' . $row)->getFont()->setColor(new Color($info2018[$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2017[$object->id.'|1'])) {
                    $sheet->setCellValue('W' . $row, CurrencyExchangeRate::format($info2017[$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('X' . $row, CurrencyExchangeRate::format($info2017[$object->id.'|1']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('W' . $row)->getFont()->setColor(new Color($info2017[$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('X' . $row)->getFont()->setColor(new Color($info2017[$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                $sheet->getRowDimension($row)->setRowHeight(50);
                $row++;

                $sheet->setCellValue('A' . $row, $object->getName() . ' | 2+4 (Инженерия)');

                $totalCuming = ($info2017[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2018[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2019[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2020[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2021_1[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2021_2[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2022[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2023_1[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2023_2[$object->id.'|24']['cuming_amount'] ?? 0);
                $totalGeneral = ($info2017[$object->id.'|24']['general_amount'] ?? 0) + ($info2018[$object->id.'|24']['general_amount'] ?? 0) + ($info2019[$object->id.'|24']['general_amount'] ?? 0) + ($info2020[$object->id.'|24']['general_amount'] ?? 0) + ($info2021_1[$object->id.'|24']['general_amount'] ?? 0) + ($info2021_2[$object->id.'|24']['general_amount'] ?? 0) + ($info2022[$object->id.'|24']['general_amount'] ?? 0) + ($info2023_1[$object->id.'|24']['general_amount'] ?? 0) + ($info2023_2[$object->id.'|24']['general_amount'] ?? 0);
                \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral, true, false);

                $sheet->setCellValue('B' . $row, CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true));
                $sheet->setCellValue('C' . $row, number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2));
                $sheet->setCellValue('D' . $row, CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true));

                $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($totalCuming < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($totalGeneral < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                if (isset ($info2023_2[$object->id.'|24'])) {
                    $sheet->setCellValue('E' . $row, CurrencyExchangeRate::format($info2023_2[$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('F' . $row, CurrencyExchangeRate::format($info2023_2[$object->id.'|24']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('E' . $row)->getFont()->setColor(new Color($info2023_2[$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('F' . $row)->getFont()->setColor(new Color($info2023_2[$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2023_1[$object->id.'|24'])) {
                    $sheet->setCellValue('G' . $row, CurrencyExchangeRate::format($info2023_1[$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('H' . $row, CurrencyExchangeRate::format($info2023_1[$object->id.'|24']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('G' . $row)->getFont()->setColor(new Color($info2023_1[$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('H' . $row)->getFont()->setColor(new Color($info2023_1[$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2022_2[$object->id.'|24'])) {
                    $sheet->setCellValue('I' . $row, CurrencyExchangeRate::format($info2022_2[$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('J' . $row, CurrencyExchangeRate::format($info2022_2[$object->id.'|24']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('I' . $row)->getFont()->setColor(new Color($info2022_2[$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('J' . $row)->getFont()->setColor(new Color($info2022_2[$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2022_1[$object->id.'|24'])) {
                    $sheet->setCellValue('K' . $row, CurrencyExchangeRate::format($info2022_1[$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('L' . $row, CurrencyExchangeRate::format($info2022_1[$object->id.'|24']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('K' . $row)->getFont()->setColor(new Color($info2022_1[$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('L' . $row)->getFont()->setColor(new Color($info2022_1[$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2021_2[$object->id.'|24'])) {
                    $sheet->setCellValue('M' . $row, CurrencyExchangeRate::format($info2021_2[$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('N' . $row, CurrencyExchangeRate::format($info2021_2[$object->id.'|24']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('M' . $row)->getFont()->setColor(new Color($info2021_2[$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('N' . $row)->getFont()->setColor(new Color($info2021_2[$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2021_1[$object->id.'|24'])) {
                    $sheet->setCellValue('O' . $row, CurrencyExchangeRate::format($info2021_1[$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('P' . $row, CurrencyExchangeRate::format($info2021_1[$object->id.'|24']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('O' . $row)->getFont()->setColor(new Color($info2021_1[$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('P' . $row)->getFont()->setColor(new Color($info2021_1[$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2020[$object->id.'|24'])) {
                    $sheet->setCellValue('Q' . $row, CurrencyExchangeRate::format($info2020[$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('R' . $row, CurrencyExchangeRate::format($info2020[$object->id.'|24']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('Q' . $row)->getFont()->setColor(new Color($info2020[$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('R' . $row)->getFont()->setColor(new Color($info2020[$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2019[$object->id.'|24'])) {
                    $sheet->setCellValue('S' . $row, CurrencyExchangeRate::format($info2019[$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('T' . $row, CurrencyExchangeRate::format($info2019[$object->id.'|24']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('S' . $row)->getFont()->setColor(new Color($info2019[$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('T' . $row)->getFont()->setColor(new Color($info2019[$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2018[$object->id.'|24'])) {
                    $sheet->setCellValue('U' . $row, CurrencyExchangeRate::format($info2018[$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('V' . $row, CurrencyExchangeRate::format($info2018[$object->id.'|24']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('U' . $row)->getFont()->setColor(new Color($info2018[$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('V' . $row)->getFont()->setColor(new Color($info2018[$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if (isset ($info2017[$object->id.'|24'])) {
                    $sheet->setCellValue('W' . $row, CurrencyExchangeRate::format($info2017[$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue('X' . $row, CurrencyExchangeRate::format($info2017[$object->id.'|24']['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle('W' . $row)->getFont()->setColor(new Color($info2017[$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle('X' . $row)->getFont()->setColor(new Color($info2017[$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                $sheet->getRowDimension($row)->setRowHeight(50);
                $row++;
                continue;
            }

            $sheet->setCellValue('A' . $row, $object->getName());

            $totalCuming = ($info2017[$object->id]['cuming_amount'] ?? 0) + ($info2018[$object->id]['cuming_amount'] ?? 0) + ($info2019[$object->id]['cuming_amount'] ?? 0) + ($info2020[$object->id]['cuming_amount'] ?? 0) + ($info2021_1[$object->id]['cuming_amount'] ?? 0) + ($info2021_2[$object->id]['cuming_amount'] ?? 0) + ($info2022_1[$object->id]['cuming_amount'] ?? 0) + ($info2022_2[$object->id]['cuming_amount'] ?? 0) + ($info2023_1[$object->id]['cuming_amount'] ?? 0) + ($info2023_2[$object->id]['cuming_amount'] ?? 0);
            $totalGeneral = ($info2017[$object->id]['general_amount'] ?? 0) + ($info2018[$object->id]['general_amount'] ?? 0) + ($info2019[$object->id]['general_amount'] ?? 0) + ($info2020[$object->id]['general_amount'] ?? 0) + ($info2021_1[$object->id]['general_amount'] ?? 0) + ($info2021_2[$object->id]['general_amount'] ?? 0) + ($info2022_1[$object->id]['general_amount'] ?? 0) + ($info2022_2[$object->id]['general_amount'] ?? 0) + ($info2023_1[$object->id]['general_amount'] ?? 0) + ($info2023_2[$object->id]['general_amount'] ?? 0);
            \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral);

            $sheet->setCellValue('B' . $row, CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true));
            $sheet->setCellValue('C' . $row, number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2));
            $sheet->setCellValue('D' . $row, CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true));

            $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($totalCuming < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($totalGeneral < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            if (isset ($info2023_2[$object->id])) {
                $sheet->setCellValue('E' . $row, CurrencyExchangeRate::format($info2023_2[$object->id]['cuming_amount'], 'RUB', 0, true));
                $sheet->setCellValue('F' . $row, CurrencyExchangeRate::format($info2023_2[$object->id]['general_amount'], 'RUB', 0, true));

                $sheet->getStyle('E' . $row)->getFont()->setColor(new Color($info2023_2[$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('F' . $row)->getFont()->setColor(new Color($info2023_2[$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if (isset ($info2023_1[$object->id])) {
                $sheet->setCellValue('G' . $row, CurrencyExchangeRate::format($info2023_1[$object->id]['cuming_amount'], 'RUB', 0, true));
                $sheet->setCellValue('H' . $row, CurrencyExchangeRate::format($info2023_1[$object->id]['general_amount'], 'RUB', 0, true));

                $sheet->getStyle('G' . $row)->getFont()->setColor(new Color($info2023_1[$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('H' . $row)->getFont()->setColor(new Color($info2023_1[$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if (isset ($info2022_2[$object->id])) {
                $sheet->setCellValue('I' . $row, CurrencyExchangeRate::format($info2022_2[$object->id]['cuming_amount'], 'RUB', 0, true));
                $sheet->setCellValue('J' . $row, CurrencyExchangeRate::format($info2022_2[$object->id]['general_amount'], 'RUB', 0, true));

                $sheet->getStyle('I' . $row)->getFont()->setColor(new Color($info2022_2[$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('J' . $row)->getFont()->setColor(new Color($info2022_2[$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if (isset ($info2022_1[$object->id])) {
                $sheet->setCellValue('K' . $row, CurrencyExchangeRate::format($info2022_1[$object->id]['cuming_amount'], 'RUB', 0, true));
                $sheet->setCellValue('L' . $row, CurrencyExchangeRate::format($info2022_1[$object->id]['general_amount'], 'RUB', 0, true));

                $sheet->getStyle('K' . $row)->getFont()->setColor(new Color($info2022_1[$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('L' . $row)->getFont()->setColor(new Color($info2022_1[$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if (isset ($info2021_2[$object->id])) {
                $sheet->setCellValue('M' . $row, CurrencyExchangeRate::format($info2021_2[$object->id]['cuming_amount'], 'RUB', 0, true));
                $sheet->setCellValue('N' . $row, CurrencyExchangeRate::format($info2021_2[$object->id]['general_amount'], 'RUB', 0, true));

                $sheet->getStyle('M' . $row)->getFont()->setColor(new Color($info2021_2[$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('N' . $row)->getFont()->setColor(new Color($info2021_2[$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if (isset ($info2021_1[$object->id])) {
                $sheet->setCellValue('O' . $row, CurrencyExchangeRate::format($info2021_1[$object->id]['cuming_amount'], 'RUB', 0, true));
                $sheet->setCellValue('P' . $row, CurrencyExchangeRate::format($info2021_1[$object->id]['general_amount'], 'RUB', 0, true));

                $sheet->getStyle('O' . $row)->getFont()->setColor(new Color($info2021_1[$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('P' . $row)->getFont()->setColor(new Color($info2021_1[$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if (isset ($info2020[$object->id])) {
                $sheet->setCellValue('Q' . $row, CurrencyExchangeRate::format($info2020[$object->id]['cuming_amount'], 'RUB', 0, true));
                $sheet->setCellValue('R' . $row, CurrencyExchangeRate::format($info2020[$object->id]['general_amount'], 'RUB', 0, true));

                $sheet->getStyle('Q' . $row)->getFont()->setColor(new Color($info2020[$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('R' . $row)->getFont()->setColor(new Color($info2020[$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if (isset ($info2019[$object->id])) {
                $sheet->setCellValue('S' . $row, CurrencyExchangeRate::format($info2019[$object->id]['cuming_amount'], 'RUB', 0, true));
                $sheet->setCellValue('T' . $row, CurrencyExchangeRate::format($info2019[$object->id]['general_amount'], 'RUB', 0, true));

                $sheet->getStyle('S' . $row)->getFont()->setColor(new Color($info2019[$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('T' . $row)->getFont()->setColor(new Color($info2019[$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if (isset ($info2018[$object->id])) {
                $sheet->setCellValue('U' . $row, CurrencyExchangeRate::format($info2018[$object->id]['cuming_amount'], 'RUB', 0, true));
                $sheet->setCellValue('V' . $row, CurrencyExchangeRate::format($info2018[$object->id]['general_amount'], 'RUB', 0, true));

                $sheet->getStyle('U' . $row)->getFont()->setColor(new Color($info2018[$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('V' . $row)->getFont()->setColor(new Color($info2018[$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if (isset ($info2017[$object->id])) {
                $sheet->setCellValue('W' . $row, CurrencyExchangeRate::format($info2017[$object->id]['cuming_amount'], 'RUB', 0, true));
                $sheet->setCellValue('X' . $row, CurrencyExchangeRate::format($info2017[$object->id]['general_amount'], 'RUB', 0, true));

                $sheet->getStyle('W' . $row)->getFont()->setColor(new Color($info2017[$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('X' . $row)->getFont()->setColor(new Color($info2017[$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            $sheet->getRowDimension($row)->setRowHeight(50);
            $row++;
        }

        $row--;

        $sheet->getRowDimension(1)->setRowHeight(50);

        $sheet->getColumnDimension('A')->setWidth(43);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(20);
        $sheet->getColumnDimension('Q')->setWidth(20);
        $sheet->getColumnDimension('R')->setWidth(20);
        $sheet->getColumnDimension('S')->setWidth(20);
        $sheet->getColumnDimension('T')->setWidth(20);
        $sheet->getColumnDimension('U')->setWidth(20);
        $sheet->getColumnDimension('V')->setWidth(20);
        $sheet->getColumnDimension('W')->setWidth(20);
        $sheet->getColumnDimension('X')->setWidth(20);

        $sheet->getStyle('A1:X' . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(true);

        $sheet->getStyle('A1:A' . $row)->getAlignment()->setHorizontal('left');
        $sheet->getStyle('B1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('E1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('G1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('I1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('K1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('M1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('O1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('Q1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('S1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('U1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('W1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('B2:X2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C3:C' . $row)->getAlignment()->setHorizontal('center');

        $sheet->getStyle('A1:V1')->getFont()->setBold(true);
        $sheet->getStyle('B1:D' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:X2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        $sheet->getStyle('B1:D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('A1:X' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'dddddd']]]
        ]);

        $sheet->getStyle('B1:D' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('E1:F' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('G1:H' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('I1:J' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('K1:L' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('M1:N' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('O1:P' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('Q1:R' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('S1:T' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('U1:V' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('W1:X' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->freezePane('B2');
            },
        ];
    }
}
