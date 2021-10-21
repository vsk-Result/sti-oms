<?php

namespace App\Services;

use App\Models\Object\BObject;
use App\Models\Status;

class ObjectService
{
    public function createObject(array $requestData): BObject
    {
        $object = BObject::create([
            'code' => $requestData['code'],
            'name' => $requestData['name'],
            'address' => $requestData['address'],
            'photo' => $requestData['photo'],
            'status_id' => Status::STATUS_ACTIVE
        ]);

        return $object;
    }
}
