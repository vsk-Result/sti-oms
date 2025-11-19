<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Imports\UpdatePaymentImport;
use App\Models\Payment;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UpdatePaymentController extends Controller
{
    private PaymentService $paymentService;
    private OrganizationService $organizationService;

    public function __construct(PaymentService $paymentService, OrganizationService $organizationService)
    {
        $this->paymentService = $paymentService;
        $this->organizationService = $organizationService;
    }

    public function store(Request $request): RedirectResponse
    {
        $requestData = $request->toArray();
        $importData = Excel::toArray(new UpdatePaymentImport(), $requestData['file']);

        $fields = [];
        foreach ($importData['Исправить'] as $index => $row) {
            if ($index === 0) {
                foreach ($row as $field) {
                    $fields[] = $field;
                }
                continue;
            }

            $payment = Payment::find($row[0]);

            if (! $payment) {
                continue;
            }

            $fieldsToUpdate = [];
            foreach ($fields as $index => $field) {
                if ($field === 'id') {
                    continue;
                }

                if ($field === 'organization') {
                    $fieldsToUpdate['organization_id'] = $this->organizationService->getOrCreateOrganization([
                        'inn' => null,
                        'name' => $row[$index],
                        'company_id' => null,
                        'kpp' => null
                    ])->id;

                    continue;
                }

                if ($field === 'was_split') {
                    $fieldsToUpdate['was_split'] = true;

                    continue;
                }

                $fieldsToUpdate[$field] = $row[$index];
            }

            if (count($fieldsToUpdate) === 0) {
                continue;
            }

            $this->paymentService->updatePayment($payment, $fieldsToUpdate);
        }

        return redirect()->back();
    }
}
