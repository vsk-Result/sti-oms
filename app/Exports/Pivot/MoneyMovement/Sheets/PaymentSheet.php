<?php

namespace App\Exports\Pivot\MoneyMovement\Sheets;

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
    private Builder $payments;

    public function __construct(Builder $payments)
    {
        $this->payments = $payments;
    }

    public function title(): string
    {
        return 'Таблица оплат';
    }

    public function headings(): array
    {
        return [
            'Объект',
            'Название объекта',
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
        ];
    }

    public function query(): Builder
    {
        return $this->payments->orderByDesc('amount');
    }

    public function map($row): array
    {
        return [
            $row->getObject(),
            $row->getObjectName(),
            $row->amount < 0 ? 'Расход' : 'Приход',
            $row->getPaymentType(),
            Date::dateTimeToExcel(Carbon::parse($row->date)),
            $row->code . ' ',
            $row->amount,
            $row->amount_without_nds,
            $row->amount < 0 ? ($row->organizationReceiver->name ?? '') : ($row->organizationSender->name ?? ''),
            $row->description,
            $row->category,
            $row->company->name ?? '',
            $row->getBankName(),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $paymentsCount = $this->payments->count();
        $sheet->getStyle('A1:M' . ($paymentsCount + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        $sheet->setAutoFilter('A1:M' . ($paymentsCount + 1));
    }

    public function columnWidths(): array
    {
        return [
            'I' => 50,
            'G' => 50,
        ];
    }
}
