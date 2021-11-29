<?php

namespace App\Models\Object;

use App\Models\Payment;
use App\Models\PaymentImport;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BObject extends Model
{
    use SoftDeletes, HasStatus;

    protected $table = 'objects';

    protected $fillable = [
        'code',
        'name',
        'address',
        'photo',
        'status_id',
        'responsible_name',
        'responsible_email',
        'responsible_phone'
    ];

    public function imports(): HasMany
    {
        return $this->hasMany(PaymentImport::class, 'object_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'object_id');
    }

    public static function getObjectsList(): array
    {
        $result = [];
        $workTypes = WorkType::getWorkTypes();
        $objects = static::orderBy('code')->get();

        foreach ($objects as $object) {
            foreach ($workTypes as $workType) {
                $result[$object->id . '::' . $workType['id']] = $object->code . '.' . $workType['code'];
            }
        }

        return $result;
    }

    public function getPhoto(): string
    {
        return $this->photo ? "/storage/$this->photo" : asset('images/blanks/object_photo_blank.jpg');
    }

    public function getName(): string
    {
        return $this->code . ' | '  . $this->name;
    }
}
