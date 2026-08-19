<?php

namespace App\Models;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes, HasStatus, HasUser;

    protected $table = 'banks';

    protected $fillable = [
        'id', 'name', 'balance_amount', 'status_id', 'created_by_user_id',
        'updated_by_user_id', 'logo', 'balance_date'
    ];

    public static function getBanks(): array
    {
        return self::where('status_id', Status::STATUS_ACTIVE)->pluck('name', 'id')->toArray();
    }

    public static function getBankName(int $bankId): string
    {
        $bank = self::find($bankId);
        return $bank ? $bank->name : '';
    }

    public static function getBankLogo(int $bankId): string
    {
        $bank = self::find($bankId);
        return $bank ? $bank->logo : '';
    }
}
