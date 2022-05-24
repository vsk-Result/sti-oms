<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Object\BObject;
use App\Models\Status;

class ObjectService
{
    private UploadService $uploadService;
    private Sanitizer $sanitizer;

    public function __construct(UploadService $uploadService, Sanitizer $sanitizer)
    {
        $this->uploadService = $uploadService;
        $this->sanitizer = $sanitizer;
    }

    public function createObject(array $requestData): BObject
    {
        $object = BObject::create([
            'code' => $this->sanitizer->set($requestData['code'])->toCode()->get(),
            'is_without_worktype' => false,
            'name' => $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->get(),
            'address' => $this->sanitizer->set($requestData['address'])->upperCaseFirstWord()->get(),
            'responsible_name' => $this->sanitizer->set($requestData['responsible_name'])->upperCaseAllFirstWords()->get(),
            'responsible_email' => $this->sanitizer->set($requestData['responsible_email'])->toEmail()->get(),
            'responsible_phone' => $this->sanitizer->set($requestData['responsible_phone'])->toPhone()->get(),
            'closing_date' => $requestData['closing_date'] ?? null,
            'photo' => empty($requestData['photo'])
                ? null
                : $this->uploadService->uploadFile('objects/photo', $requestData['photo']),
            'status_id' => Status::STATUS_ACTIVE
        ]);

        $object->customers()->sync($requestData['customer_id'] ?? []);

        return $object;
    }

    public function updateObject(BObject $object, array $requestData): void
    {
        if (array_key_exists('photo', $requestData)) {
            $photo = $this->uploadService->uploadFile('objects/photo', $requestData['photo']);
        } elseif ($requestData['avatar_remove'] === '1') {
            $photo = null;
        } else {
            $photo = $object->photo;
        }

        $object->update([
            'code' => $this->sanitizer->set($requestData['code'])->toCode()->get(),
            'is_without_worktype' => false,
            'name' => $this->sanitizer->set($requestData['name'])->upperCaseFirstWord()->get(),
            'address' => $this->sanitizer->set($requestData['address'])->upperCaseFirstWord()->get(),
            'responsible_name' => $this->sanitizer->set($requestData['responsible_name'])->upperCaseAllFirstWords()->get(),
            'responsible_email' => $this->sanitizer->set($requestData['responsible_email'])->toEmail()->get(),
            'responsible_phone' => $this->sanitizer->set($requestData['responsible_phone'])->toPhone()->get(),
            'photo' => $photo,
            'closing_date' => array_key_exists('closing_date', $requestData) ? $requestData['closing_date'] : $object->closing_date,
            'status_id' => $requestData['status_id']
        ]);

        $object->customers()->sync($requestData['customer_id'] ?? []);
    }
}
