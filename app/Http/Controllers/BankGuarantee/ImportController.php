<?php

namespace App\Http\Controllers\BankGuarantee;

use App\Helpers\Sanitizer;
use App\Http\Controllers\Controller;
use App\Imports\Contract\ContractImport;
use App\Models\BankGuarantee;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportController extends Controller
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function store(Request $request): RedirectResponse
    {
        $requestData = $request->toArray();
        $importData = Excel::toArray(new ContractImport(), $requestData['file']);
        foreach ($importData['Банковские гарантии'] as $index => $guaranteeRow) {
            if ($index === 0) continue;

            BankGuarantee::create([
                'company_id' => 1,
                'bank_id' => null,
                'object_id' => BObject::where('code', $guaranteeRow[0])->first()->id ?? null,
                'contract_id' => Contract::where('name', $guaranteeRow[1])->first()->id ?? null,
                'organization_id' => Organization::where('name', $guaranteeRow[2])->first()->id ?? null,
                'number' => $this->sanitizer->set($guaranteeRow[3])->get(),
                'start_date' => $guaranteeRow[4] === null ? null : Carbon::parse(Date::excelToDateTimeObject($guaranteeRow[4]))->format('Y-m-d'),
                'end_date' => $guaranteeRow[5] === null ? null : Carbon::parse(Date::excelToDateTimeObject($guaranteeRow[5]))->format('Y-m-d'),
                'currency' => $guaranteeRow[6],
                'currency_rate' => 1,
                'amount' => $guaranteeRow[7],
                'start_date_deposit' => $guaranteeRow[8] === null ? null : Carbon::parse(Date::excelToDateTimeObject($guaranteeRow[8]))->format('Y-m-d'),
                'end_date_deposit' => $guaranteeRow[9] === null ? null : Carbon::parse(Date::excelToDateTimeObject($guaranteeRow[9]))->format('Y-m-d'),
                'amount_deposit' => $guaranteeRow[10],
                'commission' => $guaranteeRow[11] ?? 0,
                'target' => null,
                'status_id' => Status::STATUS_ACTIVE,
            ]);
        }

        return redirect()->back();
    }
}
