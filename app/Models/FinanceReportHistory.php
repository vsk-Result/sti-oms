<?php

namespace App\Models;

use App\Models\Object\BObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceReportHistory extends Model
{
    use SoftDeletes;

    protected $table = 'finance_report_history';

    protected $fillable = [
        'date', 'balances', 'credits', 'loans', 'deposits', 'objects', 'objects_new'
    ];

    public static function getCurrentFinanceReportForObject(BObject $object): array
    {
        $lastDate = self::select('date')->latest('date')->first()->date ?? now()->format('Y-m-d');
        $financeReportHistory = self::where('date', $lastDate)->first();
        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $years = collect($objectsInfo->years)->toArray();
        $total = $objectsInfo->total;

        $info = [];

        foreach ($years as $year => $objects) {
            foreach ($objects as $o) {
                if ($o->id === $object->id) {
                    $info = (array) $total->{$year}->{$object->code};
                    break;
                }
            }
        }

        return $info;
    }
}
