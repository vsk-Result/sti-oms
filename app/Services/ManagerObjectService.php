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
        foreach ($importData as $data) {
            $objectCode = $data[0] == '27' ? '27.1' : $data[0];
            $manager = [
                'object_code' => $data[0],
                'object_name' => $data[1],
                'object_boss' => BObject::where('code', $objectCode)->first()?->responsible_name ?? '',
                'object_boss_email' => BObject::where('code', $objectCode)->first()?->responsible_email ?? '',
                'names' => [],
                'emails' => [],
            ];

            foreach (explode("\n", $data[2]) as $managerName) {
                $manager['names'][] = $managerName;
            }

            foreach (explode("\n", $data[3]) as $managerName) {
                $manager['emails'][] = $managerName;
            }

            $managers[] = $manager;
        }

        return $managers;
    }
}
