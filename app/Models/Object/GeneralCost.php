<?php

namespace App\Models\Object;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralCost extends Model
{
    protected $table = 'object_general_costs';

    protected $fillable = ['object_id', 'amount', 'is_pinned'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public static function getObjectCodesForGeneralCosts(): array
    {
        $fixedCodes = [
            '257', '268', '288', '292', '296', '298', '303', '304', '305', '308', '309', '317', '321', '322',
            '323', '325', '327', '330', '332', '333', '334', '335', '338', '339', '341', '342',
            '344', '346', '349', '350', '352', '353', '358', '359', '360', '361', '363', '364', '365', '366', '367'
        ];

        $newCodes = [];

        foreach (BObject::where('code', '>', '369')->orderBy('code')->get() as $object) {
            if ($object->payments
                    ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                    ->sum('amount')
                > 0) {
                $newCodes[] = $object->code;
            }
        }

        return array_merge($fixedCodes, $newCodes);
    }
}
