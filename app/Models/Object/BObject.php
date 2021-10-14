<?php

namespace App\Models\Object;

use App\Models\Payment;
use App\Models\Statement;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Object\BObject
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $address
 * @property int $status_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Statement[] $statements
 * @property-read int|null $statements_count
 * @method static \Illuminate\Database\Eloquent\Builder|BObject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BObject newQuery()
 * @method static \Illuminate\Database\Query\Builder|BObject onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BObject query()
 * @method static \Illuminate\Database\Eloquent\Builder|BObject whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BObject whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BObject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BObject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BObject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BObject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BObject whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BObject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|BObject withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BObject withoutTrashed()
 * @mixin \Eloquent
 */
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
