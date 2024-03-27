<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $table = 'employees';

    protected $connection = 'mysql_crm';

    public function getFullname()
    {
        return "$this->secondname $this->firstname $this->thirdname";
    }

    public function differences(): HasMany
    {
        return $this->hasMany(Difference::class, 'e_id');
    }

    public function avanses(): HasMany
    {
        return $this->hasMany(Avans::class, 'e_id');
    }

    public function workhours(): HasMany
    {
        return $this->hasMany(Workhour::class, 'e_id');
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class, 'e_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'e_id');
    }

    public function additions(): HasMany
    {
        return $this->hasMany(Addition::class, 'e_id');
    }

    public function patents(): HasMany
    {
        return $this->hasMany(Patent::class, 'e_id');
    }

    public function getSalary($date)
    {
        $salary = $this->salaries()->where('date', '<=', $date)->latest('date')->first();
        if (!$salary) {
            $salary = $this->salaries()->where('date', '>', $date)->first();
            if (!$salary) $salary = null;
        }

        return $salary;
    }

    public function getPatentPayment($date, $objects_info = null)
    {
        $patentpayment = 0;

        if (is_null($objects_info)) {
            $patents = $this->patents()->where('date', $date)->get();

            foreach ($patents as $patent) {
                if ($patent->paycheck) {
                    $patentpayment -= $patent->price / 2;
                } else {
                    $patentpayment += $patent->price / 2;
                }
            }
        } else {

            $objects = $this->workhours()->where('date', 'LIKE', $date . '%')->groupBy('o_id')->pluck('o_id')->toArray();
            if (count($objects) == 0) {
                $patents = $this->patents()->where('date', $date)->get();

                foreach ($patents as $patent) {
                    if ($patent->paycheck) {
                        $patentpayment -= $patent->price / 2;
                    } else {
                        $patentpayment += $patent->price / 2;
                    }
                }

                return $patentpayment;
            }

            $hours = [];
            foreach ($objects as $object_id) {
                $hours[$object_id] = $this->workhours()
                    ->where('date', 'LIKE', $date . '%')
                    ->where('o_id', $object_id)
                    ->sum('hours');
            }

            $max = -1;
            $max_object_id = null;
            foreach ($hours as $key => $value) {
                if ($value > $max) {
                    $max = $value;
                    $max_object_id = $key;
                }
            }

            if ($max_object_id == $objects_info['ids'][0]) {
                $patents = $this->patents()->where('date', $date)->get();

                foreach ($patents as $patent) {
                    if ($patent->paycheck) {
                        $patentpayment -= (int) $patent->price / 2;
                    } else {
                        $patentpayment += (int) $patent->price / 2;
                    }
                }
            } else {
                $patentpayment = 0;
            }
        }

        return $patentpayment;
    }

    public function inBlackList()
    {
        return $this->in_black_list;
    }

    public function getUniqueID()
    {
        return $this->unique;
    }
}
