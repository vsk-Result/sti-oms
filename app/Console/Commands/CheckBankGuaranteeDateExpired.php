<?php

namespace App\Console\Commands;

use App\Models\Object\BObject;
use App\Services\BankGuaranteeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckBankGuaranteeDateExpired extends Command
{
    protected $signature = 'oms-imports:check-bank-guarantee-date-expired';

    protected $description = 'Проверка окончания действий банковских гарантиий';

    private BankGuaranteeService $bankGuaranteeService;

    public function __construct(BankGuaranteeService $bankGuaranteeService)
    {
        parent::__construct();
        $this->bankGuaranteeService = $bankGuaranteeService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] ' . $this->description);

        try {
            $checkInfo = $this->bankGuaranteeService->checkExpired();
        } catch (\Exception $e) {
            Log::channel('custom_imports_log')->debug('[ERROR] Не удалось провести проверку: "' . $e->getMessage());
            return 0;
        }

        foreach ($checkInfo['bg'] as $info) {
            Log::channel('custom_imports_log')->debug('[INFO] Истек срок действия банковской гарантии #' . $info['id'] . ', номер: ' . $info['number'] . ', дата окончания: ' . $info['date']);
            Log::channel('custom_imports_log')->debug('[INFO] Банковская гарантия #' . $info['id'] . ' переведена в архив');

            $object = BObject::find($info['object_id']);

            if ($object) {
                foreach ($object->users as $user) {
                    try {
                        $mes = 'Истек срок действия банковской гарантии ' . $info['number'];
                        Mail::send('emails.bank-guarantees.expired_notify', compact('info', 'mes'), function ($m) use ($info, $user, $mes) {
                            $m->from('support@crm.local', 'OMS Support');
                            $m->to('result007@yandex.ru');
                            $m->to($user->email)
                                ->subject('OMS. ' . $mes);
                        });
                    } catch(\Exception $e){
                        Log::channel('custom_imports_log')->debug('[ERROR] Не удалось отправить уведомление на ' . $user->email . ': "' . $e->getMessage());
                    }
                }
            } else {
                Log::channel('custom_imports_log')->debug('[ERROR] Не найден объект #' . $info['object_id']);
            }
        }

        foreach ($checkInfo['deposit'] as $info) {
            Log::channel('custom_imports_log')->debug('[INFO] Истек срок действия депозита по БГ#' . $info['id'] . ', номер: ' . $info['number'] . ', дата окончания: ' . $info['date']);
            Log::channel('custom_imports_log')->debug('[INFO] Банковская гарантия #' . $info['id'] . ' переведена в архив');

            $object = BObject::find($info['object_id']);

            if ($object) {
                foreach ($object->users as $user) {
                    try {
                        $mes = 'Истек срок действия депозита по БГ ' . $info['number'];
                        Mail::send('emails.bank-guarantees.expired_notify', compact('info', 'mes'), function ($m) use ($info, $user, $mes) {
                            $m->from('support@crm.local', 'OMS Support');
                            $m->to('result007@yandex.ru');
                            $m->to($user->email)
                                ->subject('OMS. ' . $mes);
                        });
                    } catch(\Exception $e){
                        Log::channel('custom_imports_log')->debug('[ERROR] Не удалось отправить уведомление на ' . $user->email . ': "' . $e->getMessage());
                    }
                }
            } else {
                Log::channel('custom_imports_log')->debug('[ERROR] Не найден объект #' . $info['object_id']);
            }
        }

        if (count($checkInfo['bg']) === 0 && count($checkInfo['deposit']) === 0) {
            Log::channel('custom_imports_log')->debug('[INFO] Истекших сроков действия не обнаружено');
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Проверка прошла успешно');

        return 0;
    }
}
