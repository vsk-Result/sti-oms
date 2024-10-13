<?php

namespace App\Exports\Object\Balance\Sheets;

use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use App\Services\PivotObjectDebtService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BalanceSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize,
    WithDrawings
{
    private BObject $object;
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(
        BObject $object,
        PivotObjectDebtService $pivotObjectDebtService,
    ) {
        $this->object = $object;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function title(): string
    {
        return 'Баланс объекта на ' . Carbon::now()->format('d.m.Y');
    }

    public function styles(Worksheet $sheet): void
    {
        $object = $this->object;
        $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $years = collect($objectsInfo->years)->toArray();
        $total = $objectsInfo->total;

        $info = [];

        foreach ($years as $year => $objects) {
            foreach ($objects as $o) {
                if ($o->id === $object->id) {
                    $info = (array) $total->{$year}->{$object->code};
                    break;
                }
            }
        }

        $debts = $this->pivotObjectDebtService->getPivotDebtForObject($this->object->id);

        $sheet->getPageSetup()->setPrintAreaByColumnAndRow(1, 1, 24, 29);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->setShowGridlines(false);
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X'];
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(false);
            $sheet->getColumnDimension($column)->setWidth(10);
        }

        $sheet->getColumnDimension('A')->setWidth(4);
        $sheet->getColumnDimension('T')->setWidth(12);
        $sheet->getColumnDimension('N')->setWidth(18);

        for ($row = 1; $row < 30; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(28);
        }

        $titleStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'f9cbad'],],],];
        $valueStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'dddddd'],],],];

        $sheet->setCellValue('E1', $object->code . ' | ' . $object->name);
        $sheet->mergeCells('E1:U3');
        $sheet->getStyle('E1:X3')->getFont()->setColor(new Color('f25a21'));
        $sheet->getStyle('E1:U3')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('E1:U3')->getFont()->setName('Calibri')->setSize(32);
        $sheet->getStyle('E1:U3')->getFont()->setBold(true);

        $sheet->setCellValue('V1', 'Дата отчета');
        $sheet->setCellValue('V2', Carbon::now()->format('d.m.Y'));
        $sheet->mergeCells('V1:X1');
        $sheet->mergeCells('V2:X2');
        $sheet->getStyle('V1:X1')->getAlignment()->setVertical('bottom')->setHorizontal('right');
        $sheet->getStyle('V2:X2')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('V2:X2')->getFont()->setBold(true);

        $sheet->setCellValue('B4', 'Код объекта');
        $sheet->setCellValue('B5', 'Название объекта');
        $sheet->setCellValue('B6', 'Адрес объекта');
        $sheet->setCellValue('B7', 'Руководитель проекта');
        $sheet->setCellValue('B8', 'Контакты руководителя');
        $sheet->mergeCells('B4:D4');
        $sheet->mergeCells('B5:D5');
        $sheet->mergeCells('B6:D6');
        $sheet->mergeCells('B7:D7');
        $sheet->mergeCells('B8:D8');
        $sheet->getStyle('B4:D8')->applyFromArray($titleStyleArray);
        $sheet->getStyle('B4:D8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('B4:D8')->getAlignment()->setVertical('center')->setHorizontal('left');

        $sheet->setCellValue('E4', $object->code);
        $sheet->setCellValue('E5', $object->name);
        $sheet->setCellValue('E6', $object->address);
        $sheet->setCellValue('E7', $object->responsible_name);
        $sheet->setCellValue('E8', $object->responsible_email);
        $sheet->mergeCells('E4:X4');
        $sheet->mergeCells('E5:X5');
        $sheet->mergeCells('E6:X6');
        $sheet->mergeCells('E7:X7');
        $sheet->mergeCells('E8:X8');
        $sheet->getStyle('E4:X8')->applyFromArray($valueStyleArray);
        $sheet->getStyle('E4:X8')->getAlignment()->setVertical('center')->setHorizontal('left');



        $sheet->setCellValue('B10', 'Приходы');
        $sheet->setCellValue('B11', 'Расходы');
        $sheet->setCellValue('B12', 'Сальдо без общ. Расходов');
        $sheet->setCellValue('B13', 'Общие расходы');
        $sheet->setCellValue('B14', 'Сальдо c общ. Расходами');
        $sheet->mergeCells('B10:D10');
        $sheet->mergeCells('B11:D11');
        $sheet->mergeCells('B12:D12');
        $sheet->mergeCells('B13:D13');
        $sheet->mergeCells('B14:D14');
        $sheet->getStyle('B10:D14')->applyFromArray($titleStyleArray);
        $sheet->getStyle('B10:D14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('B10:D14')->getAlignment()->setVertical('center')->setHorizontal('left');

        $this->setValueEndColor($sheet, 'E10', $info['receive']);
        $this->setValueEndColor($sheet, 'E11', $info['pay']);
        $this->setValueEndColor($sheet, 'E12', $info['balance']);
        $this->setValueEndColor($sheet, 'E13', $info['general_balance']);
        $this->setValueEndColor($sheet, 'E14', $info['balance_with_general_balance']);

        $sheet->setCellValue('G13', number_format($info['general_balance_to_receive_percentage'], 2) . ' %');

        $sheet->getStyle('G13')->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('G13:G13')->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->mergeCells('E10:F10');
        $sheet->mergeCells('E11:F11');
        $sheet->mergeCells('E12:F12');
        $sheet->mergeCells('E13:F13');
        $sheet->mergeCells('E14:F14');
        $sheet->getStyle('E10:F14')->applyFromArray($valueStyleArray);
        $sheet->getStyle('E10:F14')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('E10:F14')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);



        $sheet->setCellValue('H10', 'Долг подрядчикам');
        $sheet->setCellValue('H11', 'Долг подрядчикам за ГУ');
        $sheet->setCellValue('H12', 'Долг поставщикам');
        $sheet->setCellValue('H13', 'Долг за услуги');
        $sheet->setCellValue('H14', 'Долг на зарплаты ИТР');
        $sheet->mergeCells('H10:J10');
        $sheet->mergeCells('H11:J11');
        $sheet->mergeCells('H12:J12');
        $sheet->mergeCells('H13:J13');
        $sheet->mergeCells('H14:J14');
        $sheet->getStyle('H10:J14')->applyFromArray($titleStyleArray);
        $sheet->getStyle('H10:J14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('H10:J14')->getAlignment()->setVertical('center')->setHorizontal('left');

        $this->setValueEndColor($sheet, 'K10', $info['contractor_debt']);
        $this->setValueEndColor($sheet, 'K11', $info['contractor_debt_gu']);
        $this->setValueEndColor($sheet, 'K12', $info['provider_debt']);
        $this->setValueEndColor($sheet, 'K13', $info['service_debt']);
        $this->setValueEndColor($sheet, 'K14', 0);
        $sheet->mergeCells('K10:L10');
        $sheet->mergeCells('K11:L11');
        $sheet->mergeCells('K12:L12');
        $sheet->mergeCells('K13:L13');
        $sheet->mergeCells('K14:L14');
        $sheet->getStyle('K10:L14')->applyFromArray($valueStyleArray);
        $sheet->getStyle('K10:L14')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('K10:L14')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);



        $sheet->setCellValue('N10', 'Долг на зарплаты рабочим');
        $sheet->setCellValue('N11', 'Долг Заказчика за выпол.работы');
        $sheet->setCellValue('N12', 'Долг Заказчика за ГУ (фактич.удерж.)');
        $sheet->setCellValue('N13', 'Текущий Баланс объекта');
        $sheet->setCellValue('N14', 'Прогнозируемые затраты');
        $sheet->mergeCells('N10:P10');
        $sheet->mergeCells('N11:P11');
        $sheet->mergeCells('N12:P12');
        $sheet->mergeCells('N13:P13');
        $sheet->mergeCells('N14:P14');
        $sheet->getStyle('N10:P14')->applyFromArray($titleStyleArray);
        $sheet->getStyle('N10:P14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('N10:P14')->getAlignment()->setVertical('center')->setHorizontal('left');

        $this->setValueEndColor($sheet, 'Q10', $info['workers_salary_debt']);
        $this->setValueEndColor($sheet, 'Q11', $info['dolgZakazchikovZaVipolnenieRaboti']);
        $this->setValueEndColor($sheet, 'Q12', $info['dolgFactUderjannogoGU']);
        $this->setValueEndColor($sheet, 'Q13', $info['objectBalance']);
        $this->setValueEndColor($sheet, 'Q14', $info['prognoz_total']);
        $sheet->mergeCells('Q10:R10');
        $sheet->mergeCells('Q11:R11');
        $sheet->mergeCells('Q12:R12');
        $sheet->mergeCells('Q13:R13');
        $sheet->mergeCells('Q14:R14');
        $sheet->getStyle('Q10:R14')->applyFromArray($valueStyleArray);
        $sheet->getStyle('Q10:R14')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('Q10:R14')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);



        $sheet->setCellValue('T10', 'Сумма договоров с Заказчиком');
        $sheet->setCellValue('T11', 'Остаток неотработанного аванса');
        $sheet->setCellValue('T12', 'Остаток к получ. от заказчика (в т.ч. ГУ)');
        $sheet->setCellValue('T13', 'Прогнозируемый Баланс объекта');
        $sheet->mergeCells('T10:V10');
        $sheet->mergeCells('T11:V11');
        $sheet->mergeCells('T12:V12');
        $sheet->mergeCells('T13:V13');
        $sheet->getStyle('T10:V13')->applyFromArray($titleStyleArray);
        $sheet->getStyle('T10:V13')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('T10:V13')->getAlignment()->setVertical('center')->setHorizontal('left');

        $this->setValueEndColor($sheet, 'W10', $info['contractsTotalAmount']);
        $this->setValueEndColor($sheet, 'W11', $info['ostatokNeotrabotannogoAvansa']);
        $this->setValueEndColor($sheet, 'W12', $info['ostatokPoDogovoruSZakazchikom']);
        $this->setValueEndColor($sheet, 'W13', $info['prognozBalance']);
        $sheet->mergeCells('W10:X10');
        $sheet->mergeCells('W11:X11');
        $sheet->mergeCells('W12:X12');
        $sheet->mergeCells('W13:X13');
        $sheet->getStyle('W10:X13')->applyFromArray($valueStyleArray);
        $sheet->getStyle('W10:X13')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('W10:X13')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        if ($object->free_limit_amount != 0) {
            $sheet->setCellValue('T10', 'Сумма договоров с Заказчиком');
            $sheet->setCellValue('T11', 'Остаток неотработанного аванса');
            $sheet->setCellValue('T12', 'Сумма свободного лимита АВ к получению');
            $sheet->setCellValue('T13', 'Остаток к получ. от заказчика (в т.ч. ГУ)');
            $sheet->setCellValue('T14', 'Прогнозируемый Баланс объекта');
            $sheet->mergeCells('T10:V10');
            $sheet->mergeCells('T11:V11');
            $sheet->mergeCells('T12:V12');
            $sheet->mergeCells('T13:V13');
            $sheet->mergeCells('T14:V14');
            $sheet->getStyle('T10:V14')->applyFromArray($titleStyleArray);
            $sheet->getStyle('T10:V14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
            $sheet->getStyle('T10:V14')->getAlignment()->setVertical('center')->setHorizontal('left');

            $this->setValueEndColor($sheet, 'W10', $info['contractsTotalAmount']);
            $this->setValueEndColor($sheet, 'W11', $info['ostatokNeotrabotannogoAvansa']);
            $this->setValueEndColor($sheet, 'W12', $object->free_limit_amount);
            $this->setValueEndColor($sheet, 'W13', $info['ostatokPoDogovoruSZakazchikom']);
            $this->setValueEndColor($sheet, 'W14', $info['prognozBalance']);
            $sheet->mergeCells('W10:X10');
            $sheet->mergeCells('W11:X11');
            $sheet->mergeCells('W12:X12');
            $sheet->mergeCells('W13:X13');
            $sheet->mergeCells('W14:X14');
            $sheet->getStyle('W10:X14')->applyFromArray($valueStyleArray);
            $sheet->getStyle('W10:X14')->getAlignment()->setVertical('center')->setHorizontal('right');
            $sheet->getStyle('W10:X14')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        $sheet->getRowDimension(16)->setRowHeight(48);
        $sheet->getStyle('B15:X15')->applyFromArray([ 'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'dddddd'],],],]);
        $sheet->getStyle('B16:X16')->getFont()->setName('Calibri')->setSize(20);
        $sheet->getStyle('B17:X17')->getFont()->setName('Calibri')->setSize(14);

        $sheet->setCellValue('B16', 'Долг подрядчикам');
        $sheet->setCellValue('H16', 'Долг подрядчикам за ГУ');
        $sheet->setCellValue('N16', 'Долг поставщикам');
        $sheet->setCellValue('T16', 'Долг за услуги');
        $sheet->mergeCells('B16:F16');
        $sheet->mergeCells('H16:L16');
        $sheet->mergeCells('N16:R16');
        $sheet->mergeCells('T16:X16');
        $sheet->getStyle('B16:X16')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('B16:X16')->getFont()->setColor(new Color('f25a21'));
        $sheet->getStyle('B16:X17')->getFont()->setBold(true);

        $sheet->setCellValue('B17', 'Контрагент');
        $sheet->setCellValue('E17', 'Сумма долга');
        $sheet->setCellValue('B28', 'ОСТАЛЬНЫЕ');
        $sheet->setCellValue('B29', 'ИТОГО');
        $sheet->mergeCells('B17:D17');
        $sheet->mergeCells('E17:F17');
        $sheet->mergeCells('B28:D28');
        $sheet->mergeCells('B29:D29');
        $sheet->mergeCells('E28:F28');
        $sheet->mergeCells('E29:F29');
        $sheet->getStyle('B17:D29')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('E17:F29')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('B28:F29')->getFont()->setBold(true);

        $sheet->setCellValue('H17', 'Контрагент');
        $sheet->setCellValue('K17', 'Сумма долга');
        $sheet->setCellValue('H28', 'ОСТАЛЬНЫЕ');
        $sheet->setCellValue('H29', 'ИТОГО');
        $sheet->mergeCells('H17:J17');
        $sheet->mergeCells('K17:L17');
        $sheet->mergeCells('H28:J28');
        $sheet->mergeCells('H29:J29');
        $sheet->mergeCells('K28:L28');
        $sheet->mergeCells('K29:L29');
        $sheet->getStyle('H17:J29')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('K17:L29')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('H28:K29')->getFont()->setBold(true);

        $sheet->setCellValue('N17', 'Контрагент');
        $sheet->setCellValue('O17', 'Сумма (фикс)');
        $sheet->setCellValue('Q17', 'Сумма (изм)');
        $sheet->setCellValue('N28', 'ОСТАЛЬНЫЕ');
        $sheet->setCellValue('N29', 'ИТОГО');
        $sheet->mergeCells('O17:P17');
        $sheet->mergeCells('Q17:R17');
        $sheet->mergeCells('O28:P28');
        $sheet->mergeCells('O29:P29');
        $sheet->mergeCells('Q28:R28');
        $sheet->mergeCells('Q29:R29');
        $sheet->getStyle('N17:N29')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('O17:P29')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('Q17:R29')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('N28:R29')->getFont()->setBold(true);

        $sheet->setCellValue('T17', 'Контрагент');
        $sheet->setCellValue('W17', 'Сумма долга');
        $sheet->setCellValue('T28', 'ОСТАЛЬНЫЕ');
        $sheet->setCellValue('T29', 'ИТОГО');
        $sheet->mergeCells('T17:V17');
        $sheet->mergeCells('W17:X17');
        $sheet->mergeCells('T28:V28');
        $sheet->mergeCells('T29:V29');
        $sheet->mergeCells('W28:X28');
        $sheet->mergeCells('W29:X29');
        $sheet->getStyle('T17:V29')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('W17:X29')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('T28:X29')->getFont()->setBold(true);

        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
        $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;
        $ds = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->get();

        $totalSum = 0;
        $otherSum = 0;
        $count = 0;
        $row = 18;

        $sorted = [];
        foreach ($debts['contractor']->debts as $organization => $amount) {
            $organizationId = substr($organization, 0, strpos($organization, '::'));
            $resultAmount = $amount;
            if ($objectExistInObjectImport) {
                $resultAmount += $ds->where('organization_id', $organizationId)->sum('avans');
                $totalSum += $resultAmount;
            }

            $sorted[$organization] = $resultAmount;
        }

        asort($sorted);

        foreach ($sorted as $organization => $amount) {
            $organizationName = substr($organization, strpos($organization, '::') + 2);

            if ($amount < 1 && $amount > -1) {
                continue;
            }

            if ($count === 10) {
                $otherSum += $amount;
                continue;
            }

            $sheet->setCellValue('B' . $row, $organizationName);
            $this->setValueEndColor($sheet, 'E' . $row, $amount);
            $sheet->mergeCells('B' . $row . ':D' . $row);
            $sheet->mergeCells('E' . $row . ':F' . $row);

            $row++;
            $count++;
        }

        $this->setValueEndColor($sheet, 'E28', $otherSum);
        $this->setValueEndColor($sheet, 'E29', $totalSum);
        $sheet->getStyle('B17:F28')->applyFromArray([ 'borders' => ['horizontal' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'dddddd'],],],]);
        $sheet->getStyle('B17:F17')->applyFromArray([ 'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'f25a21'],],],]);
        $sheet->getStyle('B29:F29')->applyFromArray($titleStyleArray);
        $sheet->getStyle('B29:F29')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('E18:F29')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $totalSum = 0;
        $otherSum = 0;
        $count = 0;
        $row = 18;

        $sorted = [];
        foreach ($debts['contractor']->debts as $organization => $amount) {
            $organizationId = substr($organization, 0, strpos($organization, '::'));
            $resultAmount = 0;
            if ($objectExistInObjectImport) {
                $resultAmount += $ds->where('organization_id', $organizationId)->sum('guarantee');
                $totalSum += $resultAmount;
            }

            $sorted[$organization] = $resultAmount;
        }

        asort($sorted);

        foreach ($sorted as $organization => $amount) {
            $organizationName = substr($organization, strpos($organization, '::') + 2);

            if ($amount < 1 && $amount > -1) {
                continue;
            }

            if ($count === 10) {
                $otherSum += $amount;
                continue;
            }

            $sheet->setCellValue('H' . $row, $organizationName);
            $this->setValueEndColor($sheet, 'K' . $row, $amount);
            $sheet->mergeCells('H' . $row . ':J' . $row);
            $sheet->mergeCells('K' . $row . ':L' . $row);

            $row++;
            $count++;
        }

        $this->setValueEndColor($sheet, 'K28', $otherSum);
        $this->setValueEndColor($sheet, 'K29', $totalSum);
        $sheet->getStyle('H17:L28')->applyFromArray([ 'borders' => ['horizontal' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'dddddd'],],],]);
        $sheet->getStyle('H17:L17')->applyFromArray([ 'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'f25a21'],],],]);
        $sheet->getStyle('H29:L29')->applyFromArray($titleStyleArray);
        $sheet->getStyle('H29:L29')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('K18:L29')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


        $providerDebts = [];
        foreach ($debts['provider']->debts_fix as $debt) {
            $organizationName = $debt->organization->name;
            if (!isset($providerDebts[$organizationName])) {
                $providerDebts[$organizationName] = [
                    'fix' => 0,
                    'float' => 0,
                    'total' => 0,
                    'organization_id' => $debt->organization_id,
                ];
            }

            $providerDebts[$organizationName]['fix'] += $debt->amount;
            $providerDebts[$organizationName]['total'] += $debt->amount;
        }
        foreach ($debts['provider']->debts_float as $debt) {
            $organizationName = $debt->organization->name;
            if (!isset($providerDebts[$organizationName])) {
                $providerDebts[$organizationName] = [
                    'fix' => 0,
                    'float' => 0,
                    'total' => 0,
                    'organization_id' => $debt->organization_id,
                ];
            }

            $providerDebts[$organizationName]['float'] += $debt->amount;
            $providerDebts[$organizationName]['total'] += $debt->amount;
        }

        $providerTotalAmounts = array_column($providerDebts, 'total');
        array_multisort($providerTotalAmounts, SORT_ASC, $providerDebts);

        $otherFixSum = 0;
        $otherFloatSum = 0;
        $count = 0;
        $row = 18;
        foreach ($providerDebts as $organizationName => $debtAmount) {
            if ($count === 10) {
                $otherFixSum += $debtAmount['fix'];
                $otherFloatSum += $debtAmount['float'];
                continue;
            }

            $sheet->setCellValue('N' . $row, $organizationName);
            $this->setValueEndColor($sheet, 'O' . $row, $debtAmount['fix']);
            $this->setValueEndColor($sheet, 'Q' . $row, $debtAmount['float']);
            $sheet->mergeCells('O' . $row . ':P' . $row);
            $sheet->mergeCells('Q' . $row . ':R' . $row);

            $row++;
            $count++;
        }

        $this->setValueEndColor($sheet, 'O28', $otherFixSum);
        $this->setValueEndColor($sheet, 'Q28', $otherFloatSum);
        $this->setValueEndColor($sheet, 'O29', $debts['provider']->fix_amount);
        $this->setValueEndColor($sheet, 'Q29', $debts['provider']->float_amount);
        $sheet->getStyle('N17:R28')->applyFromArray([ 'borders' => ['horizontal' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'dddddd'],],],]);
        $sheet->getStyle('N17:R17')->applyFromArray([ 'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'f25a21'],],],]);
        $sheet->getStyle('N29:R29')->applyFromArray($titleStyleArray);
        $sheet->getStyle('N29:R29')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('O18:R29')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


        $otherSum = 0;
        $count = 0;
        $row = 18;
        foreach ($debts['service']->debts as $organization => $amount) {
            if ($count === 10) {
                $otherSum += $amount;
                continue;
            }

            $organizationName = substr($organization, strpos($organization, '::') + 2);

            $sheet->setCellValue('T' . $row, $organizationName);
            $this->setValueEndColor($sheet, 'W' . $row, $amount);
            $sheet->mergeCells('T' . $row . ':V' . $row);
            $sheet->mergeCells('W' . $row . ':X' . $row);

            $row++;
            $count++;
        }

        $this->setValueEndColor($sheet, 'W28', $otherSum);
        $this->setValueEndColor($sheet, 'W29', $info['service_debt']);
        $sheet->getStyle('T17:X28')->applyFromArray([ 'borders' => ['horizontal' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'dddddd'],],],]);
        $sheet->getStyle('T17:X17')->applyFromArray([ 'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'f25a21'],],],]);
        $sheet->getStyle('T29:X29')->applyFromArray($titleStyleArray);
        $sheet->getStyle('T29:X29')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('W18:X29')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('STI Logo');
        $drawing->setPath(public_path('/images/logo.png'));
        $drawing->setHeight(70);
        $drawing->setCoordinates('B1');
        $drawing->setOffsetY(20);

        return $drawing;
    }

    private function setValueEndColor(Worksheet &$sheet, string $cell, $value): void
    {
        $sheet->setCellValue($cell, number_format($value, 0, '.', ''));
        $sheet->getStyle($cell)->getFont()->setColor(new Color($value < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
    }
}
