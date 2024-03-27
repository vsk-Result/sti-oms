<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CObject extends Model
{
    protected $table = 'objects';

    protected $connection = 'mysql_crm';

    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }
}
