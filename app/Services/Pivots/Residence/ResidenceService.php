<?php

namespace App\Services\Pivots\Residence;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ResidenceService
{
    public function getDormitories(): array
    {
        $dormitories = [];

        try {
            $response = Http::withBasicAuth('WebService', 'Vi7je7da')->post('https://1c.st-ing.com/prod_STI_usp/hs/USPServices/Universal', [
                'Method' => 'GetDormitories',
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $dormitories = $data['Result'];
        } catch (\Exception $e) {}

        if (empty($dormitories)) {
            $dormitories = [];
        }

        return $dormitories;
    }

    public function getResidenceInfo(string $date, string $dormitoryId): array
    {
        $info = [];

        try {
            $response = Http::withBasicAuth('WebService', 'Vi7je7da')->post('https://1c.st-ing.com/prod_STI_usp/hs/USPServices/Universal', [
                'Method' => 'GetAccommodation',
                'Period' => $date,
                'DormitoryId' => $dormitoryId
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (count($data) > 0) {
                foreach ($data['Result'] as $employee) {
                    if (!empty($employee['DismissalDate'])) {
                        $count = 0;
                        $sum = 0;

                        foreach ($employee['Details'] as $date_info) {
                            if (Carbon::parse($date_info['Date']) > Carbon::parse($employee['DismissalDate'])) {
                                $count++;
                                $sum += $date_info['Tarif'];
                            }
                        }

                        $info[] = [
                            'unique' => $employee['UID'],
                            'name' => $employee['Person'],
                            'object' => $employee['Object'],
                            'dismissal_date' => $employee['DismissalDate'],
                            'status' => $employee['Status'],
                            'daysLivedIllegally' => $count,
                            'sumLivedIllegally' => $sum
                        ];
                    }
                }
            }
        } catch (\Exception $e) {}

        return $info;
    }
}
