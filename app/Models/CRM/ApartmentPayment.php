<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class ApartmentPayment extends Model
{
    protected $table = 'apartment_payments';

    public $timestamps = false;

    protected $connection = 'mysql_crm';
}
