<?php

namespace App\Exports\Payment\Sheets;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
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

class PaymentObjectSheet implements
    WithTitle,
    WithHeadings,
    FromCollection,
    WithMapping,
    WithColumnFormatting,
    ShouldAutoSize,
    WithStyles,
    WithColumnWidths
{
    private string $sheetName;

    private Collection $payments;

    public function __construct(string $sheetName, Collection $payments)
    {
        $this->sheetName = $sheetName;
        $this->payments = $payments;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function headings(): array
    {
        return [
            'Объект',
            'Тип',
            'Дата счета',
            'Дата оплаты',
            'Код',
            'Код затрат',
            'Сумма',
            'Аванс',
            'Остаток',
            'Валюта',
            'Курс валюты',
            '№ счета',
            'Контрагент',
            'Описание',
            'Компания',
            'Вид расхода',
            'Банк',
        ];
    }

    public function collection(): Collection
    {
        return $this->payments;
    }

    public function map($row): array
    {
        return [
            $row->getObject(),
            $row->amount < 0 ? 'Payable' : 'Receivable',
            '',
            Date::dateTimeToExcel(Carbon::parse($row->date)),
            $row->object_worktype_id,
            $row->code,
            $row->amount,
            $row->amount,
            '0',
            'RUB',
            '1.0000',
            '',
            $row->amount < 0 ? $row->organizationReceiver->name : $row->organizationSender->name,
            $row->description,
            $row->company->short_name,
            $row->category,
            $row->getBankName(),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_00,
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

    public function columnWidths(): array
    {
        return [
            'N' => 55,
        ];
    }
}
