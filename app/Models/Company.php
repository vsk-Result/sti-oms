<?php

namespace App\Models;

use App\Models\Object\BObject;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes, HasStatus;

    protected $table = 'companies';

    protected $fillable = ['name', 'short_name', 'inn', 'status_id'];

    public function objects(): HasMany
    {
        return $this->hasMany(BObject::class, 'company_id');
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'company_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'company_id');
    }

    public function bankGuarantees(): HasMany
    {
        return $this->hasMany(BankGuarantee::class, 'company_id');
    }

    public function statements(): HasMany
    {
        return $this->hasMany(PaymentImport::class, 'company_id');
    }

    public function getShortNameColored(): string
    {
        switch ($this->short_name) {
            case 'СТИ': return '<span style="color: #6d6f71;">С</span><span style="color: #f15a22;">Т</span><span style="color: #6d6f71;">И</span>';
            case 'ПТИ': return '<span style="color: #586672;">П</span><span style="color: #63be60;">Т</span><span style="color: #586672;">И</span>';
        }

        return $this->short_name;
    }
}
