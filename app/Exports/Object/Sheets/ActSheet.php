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

class ActSheet implements
    WithTitle,
    WithHeadings,
    FromQuery,
    WithMapping,
    WithColumnFormatting,
    ShouldAutoSize,
    WithStyles
{
    private string $sheetName;

    private Builder $acts;

    private int $actCount;

    public function __construct(string $sheetName, Builder $acts, int $actCount)
    {
        $this->sheetName = $sheetName;
        $this->acts = $acts;
        $this->actCount = $actCount;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function headings(): array
    {
        return [
            'Договор',
            'Номер акта',
            'Дата акта',
            'Выполнено',
            'Аванс удержан',
            'Депозит удержан',
            'К оплате',
            'Дата планируемой оплаты',
            'Оплачено',
            'Сумма неоплаченных работ',
            'ID акта в STI OMS',
        ];
    }

    public function query(): Builder
    {
        return $this->acts;
    }

    public function map($row): array
    {
        return [
            $row->contract->getName(),
            $row->number,
            $row->date ? Date::dateTimeToExcel(Carbon::parse($row->date)) : '',
            $row->getAmount(),
            $row->getAvansAmount(),
            $row->getDepositAmount(),
            $row->getNeedPaidAmount(),
            $row->planned_payment_date ? Date::dateTimeToExcel(Carbon::parse($row->planned_payment_date)) : '',
            $row->getPaidAmount(),
            $row->getLeftPaidAmount(),
            $row->id,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:K' . ($this->actCount + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        $sheet->setAutoFilter('A1:K' . ($this->actCount + 1));
    }
}
