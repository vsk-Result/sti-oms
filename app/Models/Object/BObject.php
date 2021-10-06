<?php

namespace App\Models\Object;

use App\Models\Payment;
use App\Models\Statement;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BObject extends Model
{
    use SoftDeletes, HasStatus;

    protected $table = 'objects';

    protected $fillable = ['code', 'name', 'address', 'status_id'];

    public function statements(): HasMany
    {
        return $this->hasMany(Statement::class, 'object_id');
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
}
