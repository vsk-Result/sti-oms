<?php

namespace App\Exports\Object\Sheets;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentSheet implements
    WithTitle,
    WithHeadings,
    FromQuery,
    WithMapping,
    WithColumnFormatting,
    ShouldAutoSize,
    WithStyles,
    WithColumnWidths
{
    private string $sheetName;

    private Builder $payments;

    private int $paymentCount;

    public function __construct(string $sheetName, Builder $payments, int $paymentCount)
    {
        $this->sheetName = $sheetName;
        $this->payments = $payments;
        $this->paymentCount = $paymentCount;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function headings(): array
    {
        return [
            'Расход/Приход',
            'Тип оплаты',
            'Дата оплаты',
            'Код затрат',
            'Сумма c НДС',
            'Сумма без НДС',
            'Контрагент',
            'Информация',
            'Категория',
            'Компания',
            'Банк',
            'ID оплаты в STI OMS',
        ];
    }

    public function query(): Builder
    {
        return $this->payments;
    }

    public function map($row): array
    {
        return [
            $row->amount < 0 ? 'Расход' : 'Приход',
            $row->getPaymentType(),
            Date::dateTimeToExcel(Carbon::parse($row->date)),
            $row->code,
            $row->amount,
            $row->amount_without_nds,
            $row->amount < 0 ? ($row->organizationReceiver->name ?? '') : ($row->organizationSender->name ?? ''),
            $row->description,
            $row->category,
            $row->company->name ?? '',
            $row->getBankName(),
            $row->id,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:L' . ($this->paymentCount + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        $sheet->setAutoFilter('A1:L' . ($this->paymentCount + 1));
    }

    public function columnWidths(): array
    {
        return [
            'G' => 50,
            'H' => 50,
        ];
    }
}
