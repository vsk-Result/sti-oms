<?php

namespace App\Services\Object;

use App\Helpers\Sanitizer;
use App\Imports\Payment\RegistryImport;
use App\Imports\Payment\SplitResidenceExcelImport;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Status;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use App\Services\UploadService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ObjectFilesService
{
    public function __construct(
        private UploadService $uploadService,
        private PaymentService $paymentService,
        private OrganizationService $organizationService,
        private Sanitizer $sanitizer,
    ) {}

    public function getObjectFiles(BObject $object): Collection
    {
       return collect(Storage::files($object->getFilesPath()))
           ->map(fn ($file) => (object) [
               'filename' => basename($file),
               'extension' => pathinfo($file, PATHINFO_EXTENSION),
               'size' => $this->formatBytes(Storage::size($file)),
               'last_modified' => Carbon::createFromTimestamp(
                   Storage::lastModified($file)
               ),
               'url' => Storage::url($file),
           ]);
    }

    public function uploadObjectFile(BObject $object, UploadedFile $file): void
    {
        $this->uploadService->uploadFile(
            $object->getFilesPath(),
            $file,
            $file->getClientOriginalName()
        );

        if (str_contains($file->getClientOriginalName(), 'Реестр оплат')) {
            $importData = Excel::toArray(new RegistryImport(), $file);
            if (isset($importData['реестр'])) {
                $paymentToDelete = Cache::get('payments_registry');

                if (isset($paymentToDelete[$object->id])) {
                    foreach ($paymentToDelete[$object->id] as $paymentId) {
                        $p = Payment::find($paymentId);

                        if ($p) {
                            $this->paymentService->destroyPayment($p);
                        }
                    }
                }

                $paymentToDelete[$object->id] = [];

                foreach ($importData['реестр'] as $rowIndex => $row) {
                    if ($rowIndex === 0 || empty($row[0])) {
                        continue;
                    }

                    $organizationSender = $this->organizationService->getOrCreateOrganization([
                        'inn' => $row[5] ?? null,
                        'name' => $row[0] ?? '',
                        'company_id' => null,
                        'kpp' => null
                    ]);

                    $organizationReceiver = $this->organizationService->getOrCreateOrganization([
                        'name' => $row[4] ?? '',
                        'inn' => null,
                        'company_id' => null,
                        'kpp' => null
                    ]);

                    $payment = $this->paymentService->createPayment([
                        'currency' => 'RUB',
                        'import_id' => null,
                        'company_id' => 1,
                        'bank_id' => null,
                        'object_id' => $object->id,
                        'object_worktype_id' => null,
                        'organization_sender_id' => $organizationSender->id,
                        'organization_receiver_id' => $organizationReceiver->id,
                        'type_id' => Payment::TYPE_OBJECT,
                        'payment_type_id' => Payment::PAYMENT_TYPE_NON_CASH,
                        'category' => '',
                        'code' => '',
                        'description' => $this->sanitizer->set($row[6] ?? '')->upperCaseFirstWord()->get(),
                        'date' => Carbon::parse(Date::excelToDateTimeObject($row[9]))->format('Y-m-d'),
                        'amount' => -$this->sanitizer->set($row['10'] ?? 0)->toAmount()->get(),
                        'parameters' => [],
                        'amount_without_nds' => -$this->sanitizer->set($row['10'] ?? 0)->toAmount()->get(),
                        'is_need_split' => false,
                        'was_split' => false,
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency_rate' => 1,
                        'currency_amount' => -$this->sanitizer->set($row['10'] ?? 0)->toAmount()->get(),
                    ]);

                    $paymentToDelete[$object->id][] = $payment->id;
                }

                Cache::put('payments_registry', $paymentToDelete);
            }
        }
    }

    public function destroyObjectFile(BObject $object, string $filename): void
    {
        $filepath = $object->getFilesPath() . '/' . $filename;

        if (Storage::exists($filepath)) {
            Storage::delete($filepath);
        }
    }

    private function formatBytes($bytes): string
    {
        $units = ['б', 'кб', 'мб', 'гб', 'тб'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
