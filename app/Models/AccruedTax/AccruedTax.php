<?php

namespace App\Models\AccruedTax;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;

class AccruedTax extends Model
{
    use HasUser;

    protected $table = 'accrued_taxes';

    protected $fillable = ['created_by_user_id', 'updated_by_user_id', 'amount', 'date', 'name'];
}
