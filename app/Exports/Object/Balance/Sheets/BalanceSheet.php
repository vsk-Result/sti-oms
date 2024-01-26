<?php

namespace App\Exports\Object\Balance\Sheets;

use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\Contract\ContractService;
use App\Services\CurrencyExchangeRateService;
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
    private CurrencyExchangeRateService $currencyExchangeRateService;
    private ContractService $contractService;

    public function __construct(
        BObject $object,
        PivotObjectDebtService $pivotObjectDebtService,
        CurrencyExchangeRateService $currencyExchangeRateService,
        ContractService $contractService,
    ) {
        $this->object = $object;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
        $this->currencyExchangeRateService = $currencyExchangeRateService;
        $this->contractService = $contractService;
    }

    public function title(): string
    {
        return 'Баланс объекта на ' . Carbon::now()->format('d.m.Y');
    }

    public function styles(Worksheet $sheet): void
    {
        $object = $this->object;

        $paymentQuery = Payment::select('object_id', 'amount');
        $objectPayments = (clone $paymentQuery)->where('object_id', $object->id)->get();

        $object->total_pay = $objectPayments->where('amount', '<', 0)->sum('amount');
        $object->total_receive = $objectPayments->sum('amount') - $object->total_pay;
        $object->total_balance = $object->total_pay + $object->total_receive;
        $object->total_with_general_balance = $object->total_pay + $object->total_receive + $object->generalCosts()->sum('amount');
        if ($object->code === '288') {
            $object->general_balance_1 = $object->generalCosts()->where('is_pinned', false)->sum('amount');
            $object->general_balance_24 = $object->generalCosts()->where('is_pinned', true)->sum('amount');
        }

        $debts = $this->pivotObjectDebtService->getPivotDebtForObject($this->object->id);
        $serviceDebtsAmount = $debts['service']->total_amount;
        $contractorDebtsAmount = $debts['contractor']->total_amount;

        $debtObjectImport = \App\Models\Debt\DebtImport::where('type_id', \App\Models\Debt\DebtImport::TYPE_OBJECT)->latest('date')->first();
        $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;

        if ($objectExistInObjectImport) {
            $contractorDebtsAvans = \App\Models\Debt\Debt::where('import_id', $debtObjectImport->id)->where('type_id', \App\Models\Debt\Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('avans');
            $contractorDebtsGU = \App\Models\Debt\Debt::where('import_id', $debtObjectImport->id)->where('type_id', \App\Models\Debt\Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('guarantee');
            $contractorDebtsAmount = $contractorDebtsAmount + $contractorDebtsAvans + $contractorDebtsGU;
        }

        $providerDebtsAmount = $debts['provider']->total_amount;
        $ITRSalaryDebt = $object->getITRSalaryDebt();
        $workSalaryDebt = $object->getWorkSalaryDebt();
        $workSalaryDebtDetails = $object->getWorkSalaryDebtDetails();
        $customerDebtInfo = [];
        $this->contractService->filterContracts(['object_id' => [$object->id]], $customerDebtInfo);
//                $customerDebt = $customerDebtInfo['avanses_acts_left_paid_amount']['RUB'] + $customerDebtInfo['avanses_left_amount']['RUB'] + $customerDebtInfo['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');

        $contractsTotalAmount = $customerDebtInfo['amount']['RUB'];

        $dolgZakazchikovZaVipolnenieRaboti = $customerDebtInfo['avanses_acts_left_paid_amount']['RUB'];
        $dolgFactUderjannogoGU = $customerDebtInfo['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');

        // старая версия
//                $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_notwork_left_amount']['RUB'] - $customerDebtInfo['acts_amount']['RUB'];

        //новая версия
        if ($object->code === '346') {
            $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_received_amount']['RUB'] - $customerDebtInfo['avanses_acts_paid_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');
        } else {
            $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_received_amount']['RUB'] - $customerDebtInfo['avanses_acts_paid_amount']['RUB'];
        }

        $ostatokNeotrabotannogoAvansa = $customerDebtInfo['avanses_notwork_left_amount']['RUB'];

        $writeoffs = $object->writeoffs->sum('amount');

        $date = now();
        $EURExchangeRate = $this->currencyExchangeRateService->getExchangeRate($date->format('Y-m-d'), 'EUR');
        if ($EURExchangeRate) {
            $dolgZakazchikovZaVipolnenieRaboti += $customerDebtInfo['avanses_acts_left_paid_amount']['EUR'] * $EURExchangeRate->rate;
            $dolgFactUderjannogoGU += ($customerDebtInfo['avanses_acts_deposites_amount']['EUR'] - $object->guaranteePayments->where('currency', 'EUR')->sum('amount')) * $EURExchangeRate->rate;
            $ostatokNeotrabotannogoAvansa += ($customerDebtInfo['avanses_notwork_left_amount']['EUR'] * $EURExchangeRate->rate);

            // старая версия
//                    $ostatokPoDogovoruSZakazchikom += ($customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate);
//                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_notwork_left_amount']['EUR'] * $EURExchangeRate->rate);
//                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['acts_amount']['EUR'] * $EURExchangeRate->rate);

            //новая версия


            if ($object->code === '346') {
                $diff = $customerDebtInfo['amount']['EUR'] - $customerDebtInfo['avanses_received_amount']['EUR'] - $customerDebtInfo['avanses_acts_paid_amount']['EUR'];
                $ostatokPoDogovoruSZakazchikom += $diff * $EURExchangeRate->rate;
                $ostatokPoDogovoruSZakazchikom -= $object->guaranteePayments->where('currency', 'EUR')->sum('amount') * $EURExchangeRate->rate;
            } else {
                $ostatokPoDogovoruSZakazchikom += ($customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate);
                $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_received_amount']['EUR'] * $EURExchangeRate->rate);
                $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_acts_paid_amount']['EUR'] * $EURExchangeRate->rate);
            }

//                    $customerDebt += $customerDebtInfo['avanses_acts_left_paid_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt += $customerDebtInfo['avanses_left_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt += $customerDebtInfo['avanses_acts_deposites_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt -= $object->guaranteePayments->where('currency', 'EUR')->sum('amount')  * $EURExchangeRate->rate;

            $contractsTotalAmount += $customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate;
        }

        if ($object->code === '288') {
            $dolgFactUderjannogoGU = $customerDebtInfo['avanses_acts_deposites_amount']['RUB'];
        }

        if (! empty($object->closing_date) && $object->status_id === \App\Models\Status::STATUS_BLOCKED) {
            $ostatokPoDogovoruSZakazchikom = $dolgFactUderjannogoGU;
        }

        $objectBalance = $object->total_with_general_balance +
            $dolgZakazchikovZaVipolnenieRaboti +
            $dolgFactUderjannogoGU +
            $contractorDebtsAmount +
            $providerDebtsAmount +
            $serviceDebtsAmount +
            $ITRSalaryDebt +
            $workSalaryDebt +
            $writeoffs;

        $prognozBalance = $objectBalance + $ostatokPoDogovoruSZakazchikom - $dolgFactUderjannogoGU;

        // -------------------------------------------------------------------------------------------------------------------

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

        $this->setValueEndColor($sheet, 'E10', $object->total_receive);
        $this->setValueEndColor($sheet, 'E11', $object->total_pay);
        $this->setValueEndColor($sheet, 'E12', $object->total_balance);
        $this->setValueEndColor($sheet, 'E13', $object->total_with_general_balance - $object->total_balance);
        $this->setValueEndColor($sheet, 'E14', $object->total_with_general_balance);

        if ($object->total_receive == 0) {
            $sheet->setCellValue('G13', 0 . ' %');
        } else {
            $sheet->setCellValue('G13', number_format(($object->total_with_general_balance - $object->total_balance) / $object->total_receive * 100, 2) . ' %');
        }
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

        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
        $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;
        $guaranteeSum = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('guarantee');

        $this->setValueEndColor($sheet, 'K10', $objectExistInObjectImport ? $contractorDebtsAmount - $guaranteeSum : $contractorDebtsAmount);
        $this->setValueEndColor($sheet, 'K11', $objectExistInObjectImport ? $guaranteeSum : 0);
        $this->setValueEndColor($sheet, 'K12', $providerDebtsAmount);
        $this->setValueEndColor($sheet, 'K13', $serviceDebtsAmount);
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
        $sheet->mergeCells('N10:P10');
        $sheet->mergeCells('N11:P11');
        $sheet->mergeCells('N12:P12');
        $sheet->mergeCells('N13:P13');
        $sheet->getStyle('N10:P13')->applyFromArray($titleStyleArray);
        $sheet->getStyle('N10:P13')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('N10:P13')->getAlignment()->setVertical('center')->setHorizontal('left');

        $this->setValueEndColor($sheet, 'Q10', $workSalaryDebt);
        $this->setValueEndColor($sheet, 'Q11', $dolgZakazchikovZaVipolnenieRaboti);
        $this->setValueEndColor($sheet, 'Q12', $dolgFactUderjannogoGU);
        $this->setValueEndColor($sheet, 'Q13', $objectBalance);
        $sheet->mergeCells('Q10:R10');
        $sheet->mergeCells('Q11:R11');
        $sheet->mergeCells('Q12:R12');
        $sheet->mergeCells('Q13:R13');
        $sheet->getStyle('Q10:R13')->applyFromArray($valueStyleArray);
        $sheet->getStyle('Q10:R13')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('Q10:R13')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);



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

        $this->setValueEndColor($sheet, 'W10', $contractsTotalAmount);
        $this->setValueEndColor($sheet, 'W11', $ostatokNeotrabotannogoAvansa);
        $this->setValueEndColor($sheet, 'W12', $ostatokPoDogovoruSZakazchikom);
        $this->setValueEndColor($sheet, 'W13', $prognozBalance);
        $sheet->mergeCells('W10:X10');
        $sheet->mergeCells('W11:X11');
        $sheet->mergeCells('W12:X12');
        $sheet->mergeCells('W13:X13');
        $sheet->getStyle('W10:X13')->applyFromArray($valueStyleArray);
        $sheet->getStyle('W10:X13')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('W10:X13')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);



        $sheet->getRowDimension(16)->setRowHeight(48);
        $sheet->getStyle('B15:X15')->applyFromArray([ 'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'dddddd'],],],]);
        $sheet->getStyle('B16:X16')->getFont()->setName('Calibri')->setSize(20);
        $sheet->getStyle('B17:X17')->getFont()->setName('Calibri')->setSize(14);

        $sheet->setCellValue('B16', 'Долг подрядчикам');
        $sheet->setCellValue('H16', 'Долг поставщикам');
        $sheet->setCellValue('N16', 'Долг за услуги');
        $sheet->mergeCells('B16:F16');
        $sheet->mergeCells('H16:L16');
        $sheet->mergeCells('N16:R16');
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
        $sheet->setCellValue('Q17', 'Сумма долга');
        $sheet->setCellValue('N28', 'ОСТАЛЬНЫЕ');
        $sheet->setCellValue('N29', 'ИТОГО');
        $sheet->mergeCells('N17:P17');
        $sheet->mergeCells('Q17:R17');
        $sheet->mergeCells('N28:P28');
        $sheet->mergeCells('N29:P29');
        $sheet->mergeCells('Q28:R28');
        $sheet->mergeCells('Q29:R29');
        $sheet->getStyle('N17:P29')->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('Q17:R29')->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('N28:R29')->getFont()->setBold(true);

        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
        $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;
        $ds = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->get();

        $otherSum = 0;
        $count = 0;
        $row = 18;

        $sorted = [];
        foreach ($debts['contractor']->debts as $organization => $amount) {
            $organizationId = substr($organization, 0, strpos($organization, '::'));
            $resultAmount = $amount;
            if ($objectExistInObjectImport) {
                $resultAmount += $ds->where('organization_id', $organizationId)->sum('avans');
                $resultAmount += $ds->where('organization_id', $organizationId)->sum('guarantee');
            }

            $sorted[$organization] = $resultAmount;
        }

        asort($sorted);

        foreach ($sorted as $organization => $amount) {
            $organizationName = substr($organization, strpos($organization, '::') + 2);

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
        $this->setValueEndColor($sheet, 'E29', $contractorDebtsAmount);
        $sheet->getStyle('B17:F28')->applyFromArray([ 'borders' => ['horizontal' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'dddddd'],],],]);
        $sheet->getStyle('B17:F17')->applyFromArray([ 'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'f25a21'],],],]);
        $sheet->getStyle('B29:F29')->applyFromArray($titleStyleArray);
        $sheet->getStyle('B29:F29')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('E18:F29')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


        $otherSum = 0;
        $count = 0;
        $row = 18;
        foreach ($debts['provider']->debts as $organization => $amount) {
            if ($count === 10) {
                $otherSum += $amount;
                continue;
            }

            $organizationName = substr($organization, strpos($organization, '::') + 2);

            $sheet->setCellValue('H' . $row, $organizationName);
            $this->setValueEndColor($sheet, 'K' . $row, $amount);
            $sheet->mergeCells('H' . $row . ':J' . $row);
            $sheet->mergeCells('K' . $row . ':L' . $row);

            $row++;
            $count++;
        }

        $this->setValueEndColor($sheet, 'K28', $otherSum);
        $this->setValueEndColor($sheet, 'K29', $providerDebtsAmount);
        $sheet->getStyle('H17:L28')->applyFromArray([ 'borders' => ['horizontal' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'dddddd'],],],]);
        $sheet->getStyle('H17:L17')->applyFromArray([ 'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'f25a21'],],],]);
        $sheet->getStyle('H29:L29')->applyFromArray($titleStyleArray);
        $sheet->getStyle('H29:L29')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('K18:L29')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


        $otherSum = 0;
        $count = 0;
        $row = 18;
        foreach ($debts['service']->debts as $organization => $amount) {
            if ($count === 10) {
                $otherSum += $amount;
                continue;
            }

            $organizationName = substr($organization, strpos($organization, '::') + 2);

            $sheet->setCellValue('N' . $row, $organizationName);
            $this->setValueEndColor($sheet, 'Q' . $row, $amount);
            $sheet->mergeCells('N' . $row . ':P' . $row);
            $sheet->mergeCells('Q' . $row . ':R' . $row);

            $row++;
            $count++;
        }

        $this->setValueEndColor($sheet, 'Q28', $otherSum);
        $this->setValueEndColor($sheet, 'Q29', $serviceDebtsAmount);
        $sheet->getStyle('N17:R28')->applyFromArray([ 'borders' => ['horizontal' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'dddddd'],],],]);
        $sheet->getStyle('N17:R17')->applyFromArray([ 'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'f25a21'],],],]);
        $sheet->getStyle('N29:R29')->applyFromArray($titleStyleArray);
        $sheet->getStyle('N29:R29')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('fce3d6');
        $sheet->getStyle('Q18:R29')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
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
