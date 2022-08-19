<?php

namespace App\Exports\Finance\FinanceReport\Sheets;

use App\Models\CRM\ItrSalary;
use App\Models\CRM\SalaryDebt;
use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\Contract\ContractService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ObjectSheet implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    private ContractService $contractService;

    private BObject $object;

    public function __construct(string $sheetName, ContractService $contractService, BObject $object)
    {
        $this->sheetName = $sheetName;
        $this->contractService = $contractService;
        $this->object = $object;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $sheet->getSheetView()->setZoomScale(70);

        $sheet->setCellValue('A1', 'Сведения об объекте ' . $this->object->getName());
        $sheet->setCellValue('A2', 'Объект');
        $sheet->setCellValue('A3', 'Код');
        $sheet->setCellValue('A4', 'Адрес');
        $sheet->setCellValue('A5', 'Ответственное лицо');
        $sheet->setCellValue('A6', '    - Email');
        $sheet->setCellValue('A7', '    - Контактный телефон');
        $sheet->setCellValue('A8', 'Сотрудники');
        $sheet->setCellValue('A9', '    - ИТР');
        $sheet->setCellValue('A10', '   - Рабочие');
        $sheet->setCellValue('A11', 'Заказчик');
        $sheet->setCellValue('A12', 'Срок окончания работ по договору');
        $sheet->setCellValue('A13', 'Виды работ');

        $sheet->setCellValue('E2', 'Договоры и акты');
        $sheet->setCellValue('E3', 'Общая сумма договоров');
        $sheet->setCellValue('E4', 'Остаток денег к получ. с учётом ГУ');
        $sheet->setCellValue('E5', 'Сумма полученных авансов');
        $sheet->setCellValue('E6', 'Сумма оплаченных актов');
        $sheet->setCellValue('E7', 'Прогнозы, ожидания и долги');
        $sheet->setCellValue('E8', 'Сумма аванса к получению');
        $sheet->setCellValue('E9', 'Долг подписанных актов');
        $sheet->setCellValue('E10', 'Долг гарантийного удержания');
        $sheet->setCellValue('E11', 'Долг подрядчикам');
        $sheet->setCellValue('E12', 'Долг за материалы');
        $sheet->setCellValue('E13', 'Долг на зарплаты');
        $sheet->setCellValue('E14', 'Текущий баланс');
        $sheet->setCellValue('E15', 'Текущий баланс с существующими долгами');
        $sheet->setCellValue('E16', 'Общие затраты компании');
        $sheet->setCellValue('E17', 'Промежуточный баланс с текущими долгами и общими расходами компании');
        $sheet->setCellValue('E18', 'Не закрытый аванс');

        $sheet->setCellValue('B3', $this->object->code);
        $sheet->setCellValue('B4', $this->object->address);
        $sheet->setCellValue('B5', $this->object->responsible_name);
        $sheet->setCellValue('B6', $this->object->responsible_email);
        $sheet->setCellValue('B7', $this->object->responsible_phone);
        $sheet->setCellValue('B8', '');
        $sheet->setCellValue('B9', '');
        $sheet->setCellValue('B10', '');
        $sheet->setCellValue('B11', implode(', ', $this->object->customers()->pluck('name')->toArray()));
        $sheet->setCellValue('B12', $this->object->closing_date ? Carbon::parse($this->object->closing_date)->format('d.m.Y') : '');
        $sheet->setCellValue('B13', '');

        $totalInfo = [];
        $contracts = $this->contractService->filterContracts(['object_id' => [$this->object->id]], $totalInfo);
        $ITRSalaryObject = ItrSalary::where('kod', 'LIKE', '%' . $this->object->code. '%')->get();
        $ITRSalaryObjectAmount = $ITRSalaryObject->sum('paid') - $ITRSalaryObject->sum('total');
        $workSalaryObjectAmount = SalaryDebt::where('object_code', 'LIKE', '%' . $this->object->code. '%')->sum('amount');
        $contractorDebtsAmount = $this->object->getContractorDebtsAmount();
        $providerDebtsAmount = $this->object->getProviderDebtsAmount();
        $balance = Payment::select('object_id', 'amount')->where('object_id', $this->object->id)->sum('amount');

        $generalCosts = $this->object->generalCosts()->sum('amount');
        $balanceWithDebts = $totalInfo['avanses_left_amount']['RUB'] + $totalInfo['avanses_acts_left_paid_amount']['RUB'] + $totalInfo['avanses_acts_deposites_amount']['RUB'] + $contractorDebtsAmount + $providerDebtsAmount + $ITRSalaryObjectAmount + $workSalaryObjectAmount + $balance;

        $sheet->setCellValue('I3', CurrencyExchangeRate::format($totalInfo['amount']['RUB'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($totalInfo['amount']['EUR'], 'EUR', 0, true));
        $sheet->setCellValue('I4', CurrencyExchangeRate::format($totalInfo['avanses_non_closes_amount']['RUB'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($totalInfo['avanses_non_closes_amount']['EUR'], 'EUR', 0, true));
        $sheet->setCellValue('I5', CurrencyExchangeRate::format($totalInfo['avanses_received_amount']['RUB'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($totalInfo['avanses_received_amount']['EUR'], 'EUR', 0, true));
        $sheet->setCellValue('I6', CurrencyExchangeRate::format($totalInfo['avanses_acts_paid_amount']['RUB'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($totalInfo['avanses_acts_paid_amount']['EUR'], 'EUR', 0, true));
        $sheet->setCellValue('I8', CurrencyExchangeRate::format($totalInfo['avanses_left_amount']['RUB'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($totalInfo['avanses_left_amount']['EUR'], 'EUR', 0, true));
        $sheet->setCellValue('I9', CurrencyExchangeRate::format($totalInfo['avanses_acts_left_paid_amount']['RUB'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($totalInfo['avanses_acts_left_paid_amount']['EUR'], 'EUR', 0, true));
        $sheet->setCellValue('I10', CurrencyExchangeRate::format($totalInfo['avanses_acts_deposites_amount']['RUB'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($totalInfo['avanses_acts_deposites_amount']['EUR'], 'EUR', 0, true));
        $sheet->setCellValue('I11', CurrencyExchangeRate::format($contractorDebtsAmount, 'RUB', 0, true));
        $sheet->setCellValue('I12', CurrencyExchangeRate::format($providerDebtsAmount, 'RUB', 0, true));
        $sheet->setCellValue('I13', CurrencyExchangeRate::format($ITRSalaryObjectAmount + $workSalaryObjectAmount, 'RUB', 0, true));
        $sheet->setCellValue('I14', CurrencyExchangeRate::format($balance, 'RUB', 0, true));
        $sheet->setCellValue('I15', CurrencyExchangeRate::format($balanceWithDebts, 'RUB', 0, true));
        $sheet->setCellValue('I16', CurrencyExchangeRate::format($generalCosts, 'RUB', 0, true));
        $sheet->setCellValue('I17', CurrencyExchangeRate::format($balanceWithDebts + $generalCosts, 'RUB', 0, true));
        $sheet->setCellValue('I18', CurrencyExchangeRate::format($totalInfo['avanses_non_closes_amount']['RUB'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($totalInfo['avanses_non_closes_amount']['EUR'], 'EUR', 0, true));

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

        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('E2:K2');
        $sheet->mergeCells('E7:K7');

        $sheet->mergeCells('B3:D3');
        $sheet->mergeCells('B4:D4');
        $sheet->mergeCells('B5:D5');
        $sheet->mergeCells('B6:D6');
        $sheet->mergeCells('B7:D7');
        $sheet->mergeCells('B8:D8');
        $sheet->mergeCells('B9:D9');
        $sheet->mergeCells('B10:D10');
        $sheet->mergeCells('B11:D11');
        $sheet->mergeCells('B12:D12');
        $sheet->mergeCells('A13:A18');
        $sheet->mergeCells('B13:D18');

        $sheet->mergeCells('E3:H3');
        $sheet->mergeCells('E4:H4');
        $sheet->mergeCells('E5:H5');
        $sheet->mergeCells('E6:H6');
        $sheet->mergeCells('E8:H8');
        $sheet->mergeCells('E9:H9');
        $sheet->mergeCells('E10:H10');
        $sheet->mergeCells('E11:H11');
        $sheet->mergeCells('E12:H12');
        $sheet->mergeCells('E13:H13');
        $sheet->mergeCells('E14:H14');
        $sheet->mergeCells('E15:H15');
        $sheet->mergeCells('E16:H16');
        $sheet->mergeCells('E17:H17');
        $sheet->mergeCells('E18:H18');

        $sheet->mergeCells('I3:K3');
        $sheet->mergeCells('I4:K4');
        $sheet->mergeCells('I5:K5');
        $sheet->mergeCells('I6:K6');
        $sheet->mergeCells('I8:K8');
        $sheet->mergeCells('I9:K9');
        $sheet->mergeCells('I10:K10');
        $sheet->mergeCells('I11:K11');
        $sheet->mergeCells('I12:K12');
        $sheet->mergeCells('I13:K13');
        $sheet->mergeCells('I14:K14');
        $sheet->mergeCells('I15:K15');
        $sheet->mergeCells('I16:K16');
        $sheet->mergeCells('I17:K17');
        $sheet->mergeCells('I18:K18');

        $sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(3)->setRowHeight(35);
        $sheet->getRowDimension(4)->setRowHeight(35);
        $sheet->getRowDimension(5)->setRowHeight(35);
        $sheet->getRowDimension(6)->setRowHeight(35);
        $sheet->getRowDimension(7)->setRowHeight(35);
        $sheet->getRowDimension(8)->setRowHeight(35);
        $sheet->getRowDimension(9)->setRowHeight(35);
        $sheet->getRowDimension(10)->setRowHeight(35);
        $sheet->getRowDimension(11)->setRowHeight(35);
        $sheet->getRowDimension(12)->setRowHeight(35);
        $sheet->getRowDimension(13)->setRowHeight(35);
        $sheet->getRowDimension(14)->setRowHeight(35);
        $sheet->getRowDimension(15)->setRowHeight(35);
        $sheet->getRowDimension(16)->setRowHeight(35);
        $sheet->getRowDimension(17)->setRowHeight(35);
        $sheet->getRowDimension(18)->setRowHeight(35);

        $sheet->getStyle('A1:E2')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A3:A18')->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('B3:B18')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('E3:E6')->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('E8:E18')->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('I3:I18')->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(true);
        $sheet->getStyle('E7')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);

        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('E2')->getFont()->setBold(true);
        $sheet->getStyle('E7')->getFont()->setBold(true);
        $sheet->getStyle('E17')->getFont()->setBold(true);

        $sheet->getStyle('A2:K18')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A2:D2')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $sheet->getStyle('E2:K2')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $sheet->getStyle('E7:K7')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $sheet->getStyle('A2:D18')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $sheet->getStyle('E2:K18')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);
    }
}
