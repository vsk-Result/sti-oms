<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workhour extends Model
{
    protected $table = 'workhours';

    protected $connection = 'mysql_crm';

    public $timestamps = false;

    protected $fillable = ['hours', 'absence', 'shtraf', 'isConfirm', 'foreman_id'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(CObject::class, 'o_id');
    }
}
