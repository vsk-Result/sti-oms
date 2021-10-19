<?php

namespace App\Exports\Payment\Sheets;

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

class PaymentTransferSheet implements
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
        return 'Трансфер';
    }

    public function headings(): array
    {
        return [
            'COMPANY FROM',
            'COMPANY TO',
            'DESCRIPTION',
            'TRANSFER AMT',
            'DATE OUT'
        ];
    }

    public function collection(): Collection
    {
        return $this->payments;
    }

    public function map($row): array
    {
        return [
            $row->organizationSender->name,
            $row->organizationReceiver->name,
            $row->description,
            $row->amount,
            Date::dateTimeToExcel(Carbon::parse($row->date))
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
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
