<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Difference extends Model
{
    protected $table = 'differences';

    protected $connection = 'mysql_crm';

    protected $fillable = ['finance_flag', 'code', 'date', 'id'];

    public $timestamps = false;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'e_id');
    }
}
