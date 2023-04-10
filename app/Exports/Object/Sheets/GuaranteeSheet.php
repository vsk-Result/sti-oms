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

class GuaranteeSheet implements
    WithTitle,
    WithHeadings,
    FromQuery,
    WithMapping,
    WithColumnFormatting,
    ShouldAutoSize,
    WithStyles
{
    private string $sheetName;

    private Builder $guarantees;

    private int $guaranteeCount;

    public function __construct(string $sheetName, Builder $guarantees, int $guaranteeCount)
    {
        $this->sheetName = $sheetName;
        $this->guarantees = $guarantees;
        $this->guaranteeCount = $guaranteeCount;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function headings(): array
    {
        return [
            'Договор',
            'Заказчик',
            'Валюта',
            'Сумма ГУ (по договору)',
            'Сумма ГУ (по факту)',
            'БГ по условиям договора',
            'Итоговый акт',
            'Статус',
            'Условия оплаты гар.удержания и комментарии',
            'ID ГУ в STI OMS',
        ];
    }

    public function query(): Builder
    {
        return $this->guarantees;
    }

    public function map($row): array
    {
        return [
            $row->contract->getName(),
            $row->customer->name ?? '',
            $row->currency,
            $row->amount,
            $row->fact_amount,
            $row->getBankGuaranteeState(),
            $row->getFinalActState(),
            $row->state,
            $row->conditions,
            $row->id,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:J' . ($this->guaranteeCount + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        $sheet->setAutoFilter('A1:J' . ($this->guaranteeCount + 1));
    }
}
