<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Helpers\Sanitizer;
use App\Models\Payment;
use App\Services\PaymentService;
use Carbon\Carbon;

class UpdatePaymentNDSFromDescription extends HandledCommand
{
    protected $signature = 'oms:update-payment-nds-from-description';

    protected $description = 'Обновляет сумму без ндс в оплатах по описанию';

    protected string $period = 'Вручную';

    public function __construct(private PaymentService $paymentService, private Sanitizer $sanitizer)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $this->sendInfoMessage('Старт обновления суммы без ндс оплат');

        $payments = Payment::whereBetween('date', ['2025-01-01', '2026-01-31'])
            ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
            ->take(50)
            ->get();

        $info = [];
        foreach ($payments as $payment) {
            $hasNDS = $this->paymentService->checkHasNDSFromDescription($payment->description);

            if ($hasNDS) {
                $description = $this->sanitizer->set($payment->description)->noSpaces()->lowerCase()->get();

                $description = str_replace('.00', '', $description);

                $ndsPos = mb_strpos($description, 'вт.ч.ндс');

                if ($ndsPos === false) {
                    $ndsPos = mb_strpos($description, 'втомчислендс');
                }

                if ($ndsPos === false) {
                    $percent = $this->getNDS($payment->date);
                    $info[] = [
                        'payment' => $payment,
                        'nds' => $percent,
                    ];
                    continue;
                }

                $percentPos = mb_strpos($description, '%', $ndsPos);

                $percent = mb_substr($description, $percentPos - 4, 4);

                if (empty($percent)) {
                    $percent = $this->getNDS($payment->date);
                    $info[] = [
                        'payment' => $payment,
                        'nds' => $percent,
                    ];
                    continue;
                }

                $percent = (int) $this->sanitizer->set($percent)->toNumber()->get();

                $info[] = [
                    'payment' => $payment,
                    'nds' => $percent,
                ];
            }
        }

        foreach ($info as $paymentInfo) {
            $payment = $paymentInfo['payment'];
            $amount = (float) $payment->amount;
            $amountNDS = $amount * $paymentInfo['nds'] / (100 + $paymentInfo['nds']);

            $payment->update([
                'amount_without_nds' => $amount - $amountNDS
            ]);
        }

        $this->sendInfoMessage('Изменение суммы без ндс оплат завершено');

        $this->endProcess();

        return 0;
    }

    public function getNDS($date): int
    {
        if (Carbon::parse($date)->year < 2026) {
            return 20;
        }

        return 22;
    }
}
