<?php

namespace App\Exports\Statement\Sheets;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentGeneralSheet implements
    WithTitle,
    WithHeadings,
    FromCollection,
    WithMapping,
    WithColumnFormatting,
    ShouldAutoSize,
    WithStyles
{
    private Collection $payments;

    public function __construct(Collection $payments)
    {
        $this->payments = $payments;
    }

    public function title(): string
    {
        return 'Общее';
    }

    public function headings(): array
    {
        return [
            'DATE OF COST',
            'DATE OF TRANSFER',
            'AMOUNT',
            'KOST KOD',
            'ACCOUNT #',
            'INFO',
            'COMPANY'
        ];
    }

    public function collection(): Collection
    {
        return $this->payments;
    }

    public function map($row): array
    {
        return [
            Date::dateTimeToExcel(Carbon::parse($row->date)),
            Date::dateTimeToExcel(Carbon::parse($row->date)),
            $row->amount,
            $row->code,
            $row->company->short_name,
            $row->description,
            $row->organizationReceiver->name,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $styles = [];
        $count = count($this->payments);
        for ($i = 0; $i <= $count; $i++) {
            $styles[$i + 1] = [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
        }

        $styles[1] += ['font' => ['bold' => true]];

        return $styles;
    }
}
