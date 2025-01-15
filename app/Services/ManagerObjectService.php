<?php

namespace App\Services;

use App\Imports\ManagerObject\ManagerObjectImport;
use App\Models\Object\BObject;
use Maatwebsite\Excel\Facades\Excel;

class ManagerObjectService
{
    private const FILE_PATH = 'public/objects-debts-manuals/';
    private const FILE_NAME = 'managers_objects.xlsx';

    public function getManagers(): array
    {
        $importData = Excel::toArray(new ManagerObjectImport(), storage_path() . '/app/public/' . self::FILE_PATH . self::FILE_NAME);
        $importData = $importData['Лист1'];

        unset($importData[0]);


        $managers = [];
        $currentObject = null;
        $currentManager = null;
        foreach ($importData as $index => $data) {
            $objectCode = $data[0] == '27' ? '27.1' : $data[0];

            if ($objectCode !== $currentObject) {
                if (! is_null($currentManager)) {
                    $managers[] = $currentManager;
                }

                $object = BObject::where('code', $objectCode)->first();

                $manager = [
                    'object_code' => $data[0],
                    'object_name' => $data[1],
                    'object_boss' => $object?->responsible_name ?? '',
                    'object_boss_email' => $object?->responsible_email ?? '',
                    'object_boss_phone' => $object?->responsible_phone ?? '',
                    'names' => [],
                    'emails' => [],
                    'phones' => [],
                ];

                $currentObject = $objectCode;
                $currentManager = $manager;
            }

            $currentManager['names'][] = $data[2];
            $currentManager['emails'][] = $data[3];
            $currentManager['phones'][] = $data[4];

            if ($index === count($importData)) {
                $managers[] = $currentManager;
            }
        }

        return $managers;
    }
}
