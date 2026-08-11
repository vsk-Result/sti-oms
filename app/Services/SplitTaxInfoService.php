<?php

namespace App\Services;

use App\Models\Object\BObject;
use Illuminate\Support\Facades\Http;

class SplitTaxInfoService
{
    public function getSplitInfo(): array
    {
        try {
            $response = Http::withBasicAuth('WebService', 'Vi7je7da')->post('https://1c.st-ing.com/prod_STI_ushr/hs/Telebot/Universal', [
                'Method' => 'GetExpenses',
            ]);

            $data = json_decode($response->getBody()->getContents(), true);


            $results = $data['Result'];
        } catch (\Exception $e) {
            return ['info' => [], 'status' => 'error', 'message' => 'Не удалось получить данные из 1С'];
        }

        $info = [];
        foreach ($results as $result) {
            $m = substr($result['Месяц'], 0, 10);
            $code = '27.1 | СТИ | Затраты офиса';

            if (! empty($result['ОбъектРабот']) &&  $result['ОбъектРабот'] !== '27') {
                $object = BObject::where('code', $result['ОбъектРабот'])->first();
                $code = $object ? $object->getName() : 'Не определен';
            }

            if (empty ($info[$m]['ndfl'][$code])) {
                $info[$m]['ndfl'][$code] = 0;
            }

            if (empty ($info[$m]['strah'][$code])) {
                $info[$m]['strah'][$code] = 0;
            }

            $info[$m]['ndfl'][$code] += $result['НДФЛ'];
            $info[$m]['strah'][$code] += $result['СтраховыеВзносы'];
        }

        return ['info' => $info, 'status' => 'success', 'message' => ''];
    }
}