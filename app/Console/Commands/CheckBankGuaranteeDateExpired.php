<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Object\BObject;
use App\Services\BankGuaranteeService;
use App\Services\DepositService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CheckBankGuaranteeDateExpired extends HandledCommand
{
    protected $signature = 'oms:check-bank-guarantee-date-expired';

    protected $description = 'Проверка окончания сроков действий банковских гарантиий';

    protected string $period = 'Ежедневно в 07:00';

    public function __construct(
        private BankGuaranteeService $bankGuaranteeService,
        private DepositService $depositService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        try {
            $checkInfo = $this->bankGuaranteeService->checkExpired();
            $depositsCheckInfo = $this->depositService->checkExpired();
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось провести проверку: ' . $e->getMessage());
            $this->endProcess();
            return 0;
        }

        $message = '';
        $expiredBGCount = 0;
        $expiredDepositCount = 0;

        if (count($checkInfo['bg']) > 0) {
            $message .= '<strong>Банковские гарантии</strong>' . PHP_EOL . PHP_EOL;
        }

        foreach ($checkInfo['bg'] as $info) {
            $date = Carbon::parse($info['date'])->format('d.m.Y');
            $message .= 'Истек срок действия БГ <strong>' . $info['number'] . '</strong>, дата окончания: <strong>' . $date . '</strong>' . PHP_EOL;
            $expiredBGCount++;

            $this->sendInfoMessage('Истек срок действия банковской гарантии #' . $info['id'] . ', номер: ' . $info['number'] . ', дата окончания: ' . $date);
            $this->sendInfoMessage('Банковская гарантия #' . $info['id'] . ' переведена в архив');

            $object = BObject::find($info['object_id']);

            if ($object) {
                foreach ($object->users as $user) {
                    try {
                        $mes = 'Истек срок действия банковской гарантии ' . $info['number'];
                        Mail::send('emails.bank-guarantees.expired_notify', compact('info', 'mes'), function ($m) use ($info, $user, $mes) {
                            $m->from('support@st-ing.com', 'OMS Support');
                            $m->to('result007@yandex.ru');
                            $m->to($user->email)
                                ->subject('OMS. ' . $mes);
                        });
                    } catch(\Exception $e){
                        $this->sendErrorMessage('Не удалось отправить уведомление на ' . $user->email . ': "' . $e->getMessage());
                    }
                }
            } else {
                $this->sendErrorMessage('Не найден объект #' . $info['object_id']);
            }
        }

        if (count($checkInfo['deposit']) > 0 || count($depositsCheckInfo['deposit']) > 0) {
            $message .= '<strong>Депозиты</strong>' . PHP_EOL . PHP_EOL;
        }

        foreach ($checkInfo['deposit'] as $info) {
            $date = Carbon::parse($info['date'])->format('d.m.Y');
            $message .= 'Истек срок действия депозита по БГ <strong>' . $info['number'] . '</strong>, дата окончания: <strong>' . $date . '</strong>' . PHP_EOL;
            $expiredDepositCount++;

            $this->sendInfoMessage('Истек срок действия депозита по БГ#' . $info['id'] . ', номер: ' . $info['number'] . ', дата окончания: ' . $date);
            $this->sendInfoMessage('Банковская гарантия #' . $info['id'] . ' переведена в архив');

            $object = BObject::find($info['object_id']);

            if ($object) {
                foreach ($object->users as $user) {
                    try {
                        $mes = 'Истек срок действия депозита по БГ ' . $info['number'];
                        Mail::send('emails.bank-guarantees.expired_notify', compact('info', 'mes'), function ($m) use ($info, $user, $mes) {
                            $m->from('support@st-ing.com', 'OMS Support');
                            $m->to('result007@yandex.ru');
                            $m->to($user->email)
                                ->subject('OMS. ' . $mes);
                        });
                    } catch(\Exception $e){
                        $this->sendErrorMessage('Не удалось отправить уведомление на ' . $user->email . ': "' . $e->getMessage());
                    }
                }
            } else {
                $this->sendErrorMessage('Не найден объект #' . $info['object_id']);
            }
        }

        foreach ($depositsCheckInfo['deposit'] as $info) {
            $date = Carbon::parse($info['date'])->format('d.m.Y');
            $message .= 'Истек срок действия депозита, дата окончания: <strong>' . $date . '</strong>' . PHP_EOL;
            $expiredDepositCount++;

            $this->sendInfoMessage('Истек срок действия депозита, дата окончания: ' . $date);
            $this->sendInfoMessage('Депозит #' . $info['id'] . ' переведен в архив');

            $object = BObject::find($info['object_id']);

            if ($object) {
//                foreach ($object->users as $user) {
//                    try {
//                        $mes = 'Истек срок действия депозита';
//                        Mail::send('emails.bank-guarantees.expired_notify', compact('info', 'mes'), function ($m) use ($info, $user, $mes) {
//                            $m->from('support@st-ing.com', 'OMS Support');
//                            $m->to('result007@yandex.ru');
//                            $m->to($user->email)
//                                ->subject('OMS. ' . $mes);
//                        });
//                    } catch(\Exception $e){
//                        $this->sendErrorMessage('Не удалось отправить уведомление на ' . $user->email . ': "' . $e->getMessage());
//                    }
//                }
            } else {
                $this->sendErrorMessage('Не найден объект #' . $info['object_id']);
            }
        }

        if ($expiredBGCount === 0 && $expiredDepositCount === 0) {
            $this->sendInfoMessage('Истекших сроков действия не обнаружено');
        } else {
            $message.= PHP_EOL . '-------' . PHP_EOL;
            $message.= $expiredBGCount . ' БГ с истекшим сроком' . PHP_EOL;
            $message.= $expiredDepositCount . ' депозитов с истекшим сроком' . PHP_EOL;
            $this->sendInfoMessage($message);
        }

        $this->sendInfoMessage('Проверка прошла успешно');

        $this->endProcess();

        return 0;
    }
}
