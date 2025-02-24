<?php

namespace App\Services\CashCheck;

use App\Imports\CostManager\CostManagerImport;
use App\Models\CashCheck\CashCheck;
use App\Models\CashCheck\Manager;
use App\Models\Object\BObject;
use App\Models\User;
use App\Services\PaymentImport\Type\CRMCostClosureImportService;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class CashCheckService
{
    private const FILE_PATH = 'public/objects-debts-manuals/';
    private const FILE_NAME = 'costs_managers.xlsx';
    private const DEFAULT_MANAGER_EMAILS = ['aleksandra.kondakova@st-ing.com'];

    public function __construct(private CRMCostClosureImportService $CRMCostClosureImportService) {}

    public function createCashCheck(array $requestData): CashCheck
    {
        return CashCheck::create([
            'crm_user_id' => $requestData['crm_user_id'],
            'crm_cost_id' => $requestData['crm_cost_id'],
            'period' => $requestData['period'],
            'email_send_status_id' => CashCheck::EMAIL_SEND_STATUS_NOT_SEND,
            'status_id' => CashCheck::STATUS_UNCKECKED,
        ]);
    }

    public function findCashCheck(array $requestData): CashCheck | null
    {
        return CashCheck::where('period', $requestData['period'])->where('crm_cost_id', $requestData['crm_cost_id'])->first();
    }

    public function addCheckManagers(CashCheck $check, array $requestData): void
    {
        foreach ($requestData as $managerId) {
            $this->addCheckManager($check, ['manager_id' => $managerId]);
        }
    }

    public function addCheckManager(CashCheck $check, array $requestData): void
    {
        $manager = Manager::create([
            'check_id' => $check->id,
            'manager_id' => $requestData['manager_id'],
            'status_id' => Manager::STATUS_UNCKECKED
        ]);

        try {
            Mail::send('emails.crm-cash-check.manager_notify', compact('check'), function ($m) use ($check, $manager) {
                $m->from('support@st-ing.com', 'OMS Support');
                $m->subject('OMS. Новая заявка на закрытый период');

                $m->to($manager->manager->email);
                $m->to('result007@yandex.ru');
            });
        } catch(Exception $e){
        }
    }

    public function managerUncheck(Manager $manager): void
    {
        $manager->update([
            'status_id' => Manager::STATUS_UNCKECKED
        ]);

        $this->updateCashCheckStatus($manager->check);
    }

    public function managerCheck(Manager $manager): void
    {
        $manager->update([
            'status_id' => Manager::STATUS_CHECKED
        ]);

        $this->updateCashCheckStatus($manager->check);
    }

    public function managerRevision(Manager $manager): void
    {
        $manager->update([
            'status_id' => Manager::STATUS_REVISION
        ]);

        $this->updateCashCheckStatus($manager->check);
    }

    public function updateCashCheckStatus(CashCheck $check): void
    {
        $checkCount = 0;
        foreach ($check->managers as $manager) {
            $checkCount += (int) $manager->isChecked();
        }

        $status = CashCheck::STATUS_UNCKECKED;

        if ($checkCount === $check->managers->count()) {
            $status = CashCheck::STATUS_CHECKED;
        } elseif ($checkCount > 0) {
            $status = CashCheck::STATUS_CHECKING;
        }

        $check->update([
            'status_id' => $status
        ]);
    }

    public function getCashCheckDetails(CashCheck $check): array
    {
        $payments = [];

        if (! $check->crmCost) {
            return $payments;
        }

        $items = $check->crmCost->items()
            ->where('date', 'LIKE', $check->period . '%')
            ->where('is_close', false)
            ->with('object', 'avans')
            ->orderBy('date', 'ASC')
            ->get();

        $codesWithoutWorktype = BObject::getCodesWithoutWorktype();

        foreach ($items as $item) {
            if ($item->type_id === 1 && ! $item->object) {
                $objectCode = 'Трансфер';
                $objectName = '';
            } else if ($item->type_id === 2 && ! $item->object) {
                $objectCode = 'Трансфер';
                $objectName = '';
            } else {
                if ($item->object) {
                    if (isset($codesWithoutWorktype[$item->object->code])) {
                        $objectCode = $codesWithoutWorktype[$item->object->code];
                    } else {
                        $objectCode = $item->object->code;

                        if (str_contains($item->object->code, '.')) {
                            $objectCode = substr($objectCode, 0, strpos($objectCode, '.'));
                        }
                    }

                    $objectName = BObject::where('code', $objectCode)->first()?->name ?? 'Нет в ОМС';
                } else {
                    $objectCode = 'Нет в ОМС';
                    $objectName = '';
                }
            }

            $category = $item->category;
            if (empty($category)) {
                $category = $this->CRMCostClosureImportService->getCategoryFromKostCode($item->getKostCode(), $item->information);
            }

            $payments[] = [
                'date' => $item->date,
                'object_name' => $objectName,
                'object_code' => $objectCode,
                'code' => $item->getKostCode(),
                'organization' => $item->client,
                'description' => $item->avans ? ($item->information . ' - ' . $item->avans->employee->getFullName()) : $item->information,
                'amount' => $item->sum,
                'category' => $category
            ];
        }

        return $payments;
    }

    public function getCheckedToEmailChecks()
    {
        return CashCheck::checked()->notSended()->readyToSend()->get();
    }

    public function checkSended(CashCheck $check)
    {
        $check->update([
            'email_send_status_id' => CashCheck::EMAIL_SEND_STATUS_SEND
        ]);
    }

    public function checkSendedWithError(CashCheck $check)
    {
        $check->update([
            'email_send_status_id' => CashCheck::EMAIL_SEND_STATUS_SEND_WITH_ERROR
        ]);
    }

    public function getManagersForCheckFromExcel(CashCheck $check): array
    {
        $importData = Excel::toArray(new CostManagerImport(), storage_path() . '/app/public/' . self::FILE_PATH . self::FILE_NAME);
        $importData = $importData['Лист1'];

        unset($importData[0]);

        $managers = [];
        foreach ($importData as $data) {
            if ($check->crm_cost_id == $data[0]) {
                $managers[] = $data[3];
            }
        }

        if (count($managers) === 0) {
            $managers = User::whereIn('email', self::DEFAULT_MANAGER_EMAILS)->pluck('id')->toArray();
        }

        return $managers;
    }
}
