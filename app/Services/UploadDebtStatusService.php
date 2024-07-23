<?php

namespace App\Services;

use App\Models\Object\BObject;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UploadDebtStatusService
{
    private static array $commandsAndFiles = [
        'oms:dt-debts-from-excel' => [
            [
                'name' => 'Долги по ДТ из 1С',
                'file' => 'DT.xlsx',
                'path' => 'public/objects-debts/'
            ]
        ],
        'oms:service-debts-from-excel' => [
            [
                'name' => 'Долги за услуги из 1С',
                'file' => 'Uslugi(XLSX).xlsx',
                'path' => 'public/objects-debts/'
            ]
        ],
        'oms:contractor-debts-from-excel' => [
            [
                'name' => 'Долги по поставщикам из 1С',
                'file' => 'TabelOMS_(XLSX).xlsx',
                'path' => 'public/'
            ]
        ],
        'oms:objects-debts-from-excel' => [
            '346' => [
                'code' => '346',
                'name' => 'Долги по подрядчикам объекта 346',
                'file' => '346.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '349' => [
                'code' => '349',
                'name' => 'Долги по подрядчикам объекта 349',
                'file' => '349.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '353' => [
                'code' => '353',
                'name' => 'Долги по подрядчикам объекта 353',
                'file' => '353.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '358' => [
                'code' => '358',
                'name' => 'Долги по подрядчикам объекта 358',
                'file' => '358.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '359' => [
                'code' => '359',
                'name' => 'Долги по подрядчикам объекта 359',
                'file' => '359.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '360' => [
                'code' => '360',
                'name' => 'Долги по подрядчикам объекта 360',
                'file' => '360.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '361' => [
                'code' => '361',
                'name' => 'Долги по подрядчикам объекта 361',
                'file' => '361.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '363' => [
                'code' => '363',
                'name' => 'Долги по подрядчикам объекта 363',
                'file' => '363.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '364' => [
                'code' => '364',
                'name' => 'Долги по подрядчикам объекта 364',
                'file' => '364.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '365' => [
                'code' => '365',
                'name' => 'Долги по подрядчикам объекта 365',
                'file' => '365.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '366' => [
                'code' => '366',
                'name' => 'Долги по подрядчикам объекта 366',
                'file' => '366.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '367' => [
                'code' => '367',
                'name' => 'Долги по подрядчикам объекта 367',
                'file' => '367.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '369' => [
                'code' => '369',
                'name' => 'Долги по подрядчикам объекта 369',
                'file' => '369.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '370' => [
                'code' => '370',
                'name' => 'Долги по подрядчикам объекта 370',
                'file' => '370.xlsx',
                'path' => 'public/objects-debts/'
            ],
            '371' => [
                'code' => '371',
                'name' => 'Долги по подрядчикам объекта 371',
                'file' => '371.xlsx',
                'path' => 'public/objects-debts/'
            ]
        ],
    ];

    public function getDebtsStatus(): array
    {
        $items = [];

        $accessObjectCodes = null;
        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $accessObjectCodes = auth()->user()->objects->pluck('code');
        }

        foreach (self::$commandsAndFiles as $command => $infoArray) {
            foreach ($infoArray as $info) {
                if ($command === 'oms:objects-debts-from-excel' && !is_null($accessObjectCodes) && !in_array($info['code'], $accessObjectCodes)) {
                    continue;
                }

                $fullnameAuto = $info['path'] . $info['file'];
                $fullnameManual = 'public/objects-debts-manuals/' . $info['file'];
                $autoDate = null;
                $manualDate = null;

                if (Storage::disk('public')->exists($fullnameAuto)) {
                    $autoDate = Carbon::parse(Storage::disk('public')->lastModified($fullnameAuto));
                }

                if (Storage::disk('public')->exists($fullnameManual)) {
                    $manualDate = Carbon::parse(Storage::disk('public')->lastModified($fullnameManual));
                }

                $items[] = [
                    'name' => $info['name'],
                    'file' => $info['file'],
                    'url' => '/storage/' . $fullnameAuto,
                    'url_manual' => is_null($fullnameManual) ? null : '/storage/' . $fullnameManual,
                    'auto_date' => $autoDate,
                    'manual_date' => $manualDate,
                    'command' => $command,
                ];
            }
        }

        return $items;
    }

    public function uploadDebts(array $requestData): void
    {
        Storage::putFileAs('public/objects-debts-manuals', $requestData['file'], $requestData['filename']);
        Artisan::call($requestData['command']);
    }

    public function getUpdatedInfoForObject(BObject $object): array
    {
        $updatedInfo = [];

        $serviceInfo = self::$commandsAndFiles['oms:service-debts-from-excel'][0];
        $updatedInfo['service'] = $this->getLastUpdatedDate($serviceInfo['path'], $serviceInfo['file']);

        $providersInfo = self::$commandsAndFiles['oms:contractor-debts-from-excel'][0];
        $updatedInfo['providers'] = $this->getLastUpdatedDate($providersInfo['path'], $providersInfo['file']);

        $providersInfo = self::$commandsAndFiles['oms:objects-debts-from-excel'][$object->code];
        $updatedInfo['contractors'] = $this->getLastUpdatedDate($providersInfo['path'], $providersInfo['file']);

        return $updatedInfo;
    }

    public function getLastUpdatedDate(string $path, string $filename): string
    {
        // Путь до файла с автоматической загрузкой
        $importFilePath = storage_path() . '/app/public/' . $path . $filename;

        //Путь до файла с ручной загрузкой
        $importManualFilePath = storage_path() . '/app/public/public/objects-debts-manuals/' . $filename;

        if (! File::exists($importFilePath) && ! File::exists($importManualFilePath)) {
            return '-';
        }

        if (! File::exists($importFilePath)) {
            return Carbon::parse(File::lastModified($importManualFilePath))->format('d.m.Y');
        }

        if (! File::exists($importManualFilePath)) {
            return Carbon::parse(File::lastModified($importFilePath))->format('d.m.Y');
        }

        return File::lastModified($importManualFilePath) > File::lastModified($importFilePath)
            ? Carbon::parse(File::lastModified($importManualFilePath))->format('d.m.Y')
            : Carbon::parse(File::lastModified($importFilePath))->format('d.m.Y');
    }
}