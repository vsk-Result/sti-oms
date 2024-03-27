<?php

namespace App\Services\CRM;

use App\Models\CRM\Avans;
use App\Models\CRM\CObject;
use App\Models\CRM\Difference;
use App\Models\CRM\Employee;
use App\Models\CRM\SalaryDebt;
use App\Models\CRM\Workhour;
use App\Models\Object\BObject;
use Carbon\Carbon;

class CalculateSalaryService
{
    private $date;
    private $objects_ids;

    public function calculate()
    {
        $this->objects_ids = CObject::orderBy('code', 'ASC')->pluck('id')->toArray();

        $details = BObject::active()->first()->getWorkSalaryDebtDetails();
        foreach ($details as $detail) {
            $this->date = $detail['origin_date'];

            $this->fillDifferenceFinanceFlag();
            $this->fillAvansFinanceFlag();

            $this->renderFinances();
        }
    }

    private function renderFinances()
    {
        $finances = $this->makePayment();

        SalaryDebt::where('month', $this->date)->delete();

        $pivot = [];
        foreach ($finances as $finance) {
            if (! isset($pivot[$finance->code])) {
                $pivot[$finance->code]['total'] = 0;
                $pivot[$finance->code]['amount'] = 0;
                $pivot[$finance->code]['card'] = 0;
            }
            $pivot[$finance->code]['total'] += $finance->total;
            $pivot[$finance->code]['amount'] += -($finance->total - $finance->card - $finance->paid);
            $pivot[$finance->code]['card'] += $finance->card;
        }

        foreach ($pivot as $code => $info) {
            $debt = new SalaryDebt;
            $debt->object_code = $code;
            $debt->amount = $info['amount'];
            $debt->total_amount = $info['total'];
            $debt->card = $info['card'];
            $debt->month = $this->date;
            $debt->save();
        }
    }

    private function makePayment()
    {
        $finances = [];

        $workhour_query = Workhour::query();

        $workhour_query->whereIn('o_id', $this->objects_ids);
        $workhour_query->where('date', 'LIKE', $this->date . '%');
        $workhour_query->groupBy('e_id');

        $e_ids = $workhour_query->pluck('e_id');

        $employees = Employee::whereIn('id', $e_ids)->orderBy('secondname', 'ASC')->get();
        foreach ($employees as $employee) {
            $o_ids = Workhour::where('e_id', $employee->id)->where('date', 'LIKE', $this->date . '%')->whereIn('o_id', $this->objects_ids)->groupBy('o_id')->pluck('o_id');
            $objects = CObject::whereIn('id', $o_ids)->orderBy('code', 'ASC')->get();

            $fix_flag = null;
            $salary = $employee->getSalary($this->date);
            if ($objects->count() > 1 && $salary && $salary->type == 'Фиксированная') {
                $fix_flag = true;
            }

            foreach ($objects as $object) {
                $finance = (object)[];
                $objects_info = ['codes' => array($object->code), 'ids' => array($object->id)];
                $finance_information = $this->getFinanceInformation($employee, $this->date, $objects_info, false, false, $fix_flag);

                $finance = $this->fillFinanceFromInformation($finance, $employee, $finance_information);

                $finance = $this->fillFinancePaymentInformation($finance, $employee, $object, $this->date);

                $check = $this->checkFinance($finance);
                if ($check)
                    $finances[] = $finance;

                if ($fix_flag === true)
                    $fix_flag = false;
            }
        }

        $finances = $this->fillOtherFinances($this->date, $this->objects_ids, $e_ids, $finances);

        return $finances;
    }

    // Помечаются разницы сотрудников, у которых нет рабочих часов в указанном месяце (4 сек) - надо уменьшать
    private function fillDifferenceFinanceFlag()
    {
        $employee_query = Employee::query();

        $employee_query->whereHas('differences', function ($q) {
            $q->where('date', $this->date);
        });

        $employees = $employee_query->where('is_engeneer', false)->get();

        $difference_ids = [];
        foreach ($employees as $employee) {
            $difference_objects = $employee->differences()->where('date', $this->date)->pluck('code', 'id')->toArray();
            dd($difference_objects);
            $workhour_objects = $employee->workhours()->where('date', 'LIKE', $this->date . '%')->get()->groupBy('o_id')->pluck('o_id')->toArray();
            $workhour_objects = CObject::whereIn('id', $workhour_objects)->pluck('code')->toArray();

            foreach ($difference_objects as $difference_id => $difference_object) {
                if (!in_array($difference_object, $workhour_objects)) {
                    $difference_ids[] = $difference_id;
                }
            }
        }

        Difference::where('date', $this->date)->update(['finance_flag' => false]);
        Difference::whereIn('id', $difference_ids)->update(['finance_flag' => true]);

        return true;
    }

    // Помечаются авансы сотрудников, у которых нет рабочих часов в указанном месяце (4 сек) - надо уменьшать
    private function fillAvansFinanceFlag()
    {
        $employee_query = Employee::query();

        $employee_query->whereHas('avanses', function ($q) {
            $q->where('date', $this->date);
        });

        $employees = $employee_query->where('is_engeneer', false)->get();

        $avanses_id = [];
        foreach ($employees as $employee) {
            $avanses_objects = $employee->avanses()->where('date', $this->date)->groupBy('code')->pluck('code', 'id')->toArray();
            $workhour_objects = $employee->workhours()->where('date', 'LIKE', $this->date . '%')->groupBy('o_id')->pluck('o_id')->toArray();
            $workhour_objects = CObject::whereIn('id', $workhour_objects)->pluck('code')->toArray();

            foreach ($avanses_objects as $avanse_id => $avanse_object) {
                if (!in_array($avanse_object, $workhour_objects)) {
                    $avanses_id[] = $avanse_id;
                }
            }
        }

        Avans::where('date', $this->date)->update(['finance_flag' => false]);
        Avans::whereIn('id', $avanses_id)->update(['finance_flag' => true]);

        return true;
    }

    private function getFinanceInformation(Employee $employee, $date, $objects_info = null, $flag = false, $code_flag = false, $fix_flag = null)
    {
        $information = [];

        // Information that depends on the object
        if (is_null($objects_info)) {
            $workhours = $employee->workhours()->where('date', 'LIKE', $date . '%')->get();
            $additions = $employee->additions()->where('date', 'LIKE', $date . '%')->get();
            $information['avanscount'] = $employee->avanses()->where('date', $date)->where(function ($query) {
                $query->where('type', '!=', 'Карты')->orWhereNull('type');
            })->sum('value');
            $information['card'] = $employee->avanses()->where('date', $date)->where('type', 'Карты')->sum('value');
            $information['differencecount'] = $employee->differences()->where('date', $date)->sum('value');
            $information['paymentcount'] = $employee->payments()->where('date', $date)->sum('value');
            $information['foreman'] = '';
        } else {
            $ids = $objects_info['ids'];
            $codes = $objects_info['codes'];
            $workhours = $employee->workhours()->whereIn('o_id', $ids)->where('date', 'LIKE', $date . '%')->get();
            $additions = $employee->additions()->whereIn('o_id', $ids)->where('date', 'LIKE', $date . '%')->get();
            $information['avanscount'] = $employee->avanses()->whereIn('code', $codes)->where('date', $date)->where(function ($query) {
                $query->where('type', '!=', 'Карты')->orWhereNull('type');
            })->sum('value');
            $information['card'] = $employee->avanses()->whereIn('code', $codes)->where('date', $date)->where('type', 'Карты')->sum('value');
            $information['differencecount'] = $employee->differences()->where('date', $date)->whereIn('code', $codes)->sum('value');
            $information['paymentcount'] = $employee->payments()->where('date', $date)->whereIn('code', $codes)->sum('value');
            $foremans = $employee->workhours()->whereIn('o_id', $ids)->where('date', 'LIKE', $date . '%')->groupBy('foreman_id')->pluck('foreman_id')->toArray();
            $foreman = Employee::whereIn('id', $foremans)->first();
            $information['foreman'] = ($foreman) ? $foreman->getFullName() : '';
        }


        // The main independent information
        $information['hourscount'] = $workhours->sum('hours') + $workhours->sum('shtraf');
        $information['travelcount'] = $additions->sum('travel');
        $information['foodcount'] = $additions->sum('food');
        $information['bonuscount'] = $additions->sum('bonus');
        $information['finecount'] = $additions->sum('shtraf');
        $information['residencecount'] = $additions->sum('residence');
        if ($code_flag) {
            $information['patentpaycount'] = $employee->getPatentPayment($date, null);
        } else {
            $information['patentpaycount'] = $employee->getPatentPayment($date, $objects_info);
        }

        if (!$flag) {
            $salary = $employee->getSalary($date);

            if ($salary) {
                if ($salary->value < 10000) {
                    // Hourly
                    if ($salary->type == 'Фиксированная') {
                        $rate = 0;
                        $salary = $salary->value;
                    } else {
                        $rate = $salary->value;
                        $salary = $salary->value * $information['hourscount'];
                    }
                } else { // Fixed
                    $rate = 0;
                    $salary = $salary->value;
                }
            } else {
                $rate = 0;
                $salary = 0;
            }
        } else {
            $rate = 0;
            $salary = 0;
        }

        if ($fix_flag === false) {
            $rate = 0;
            $salary = 0;
        }

        $information['in_black_list'] = $employee->inBlackList();

        $information['rate'] = $rate;
        $information['salary'] = $salary;
        $information['total'] = $information['salary'] + $information['bonuscount']
            - $information['finecount'] + $information['travelcount']
            + $information['foodcount'] + $information['residencecount']
            + $information['patentpaycount']- $information['avanscount']
            + $information['differencecount'];

        return $information;
    }

    private function fillFinanceFromInformation($finance, Employee $employee, $finance_information)
    {
        $finance->uid = $employee->getUniqueID();
        $finance->e_id = $employee->id;
        $finance->citizenship = $employee->citizenship;
        $finance->status = $employee->status;
        $finance->name = $employee->getFullName();
        $finance->e_status = $employee->status;
        $finance->in_black_list = $finance_information['in_black_list'];
        $finance->hours = $finance_information['hourscount'];
        $finance->rate = $finance_information['rate'];
        $finance->karantinFood = isset($finance_information['karantinFood']) ? $finance_information['karantinFood'] : 0;
        $finance->full_rate = isset($finance_information['full_rate']) ? $finance_information['full_rate'] : 0;
        $finance->daysBefore = isset($finance_information['daysBefore']) ? $finance_information['daysBefore'] : 0;
        $finance->hoursBefore = isset($finance_information['hoursBefore']) ? $finance_information['hoursBefore'] : 0;
        $finance->totalBefore = isset($finance_information['totalBefore']) ? $finance_information['totalBefore'] : 0;
        $finance->daysAfter = isset($finance_information['daysAfter']) ? $finance_information['daysAfter'] : 0;
        $finance->hoursAfter = isset($finance_information['hoursAfter']) ? $finance_information['hoursAfter'] : 0;
        $finance->compInDay = isset($finance_information['compInDay']) ? $finance_information['compInDay'] : 0;
        $finance->totalAfter = isset($finance_information['totalAfter']) ? $finance_information['totalAfter'] : 0;
        $finance->salary = $finance_information['salary'];
        $finance->avans = $finance_information['avanscount'];
        $finance->travel = $finance_information['travelcount'];
        $finance->food = $finance_information['foodcount'];
        $finance->bonus = $finance_information['bonuscount'];
        $finance->fine = $finance_information['finecount'];
        $finance->residence = $finance_information['residencecount'];
        $finance->patent = $finance_information['patentpaycount'];
        $finance->difference = $finance_information['differencecount'];
        $finance->total = $finance_information['total'];
        $finance->card = $finance_information['card'];
        $finance->cash = $finance_information['total'] - $finance_information['card'];
        $finance->paid = $finance_information['paymentcount'];
        $finance->foreman = $finance_information['foreman'];

        $finance->alert = ($finance->hours >= 350) ? true : false;
        $finance->patent_alert = ($finance->hours == 0 && $finance->patent > 0) ? true : false;
        $finance->card_alert = false;
        $finance->category = (is_null($employee->category_id) || $employee->category_id == 0) ? 'Отсутствует' : $employee->category_id;
        $finance->category_alert = false;

        $cards = $employee->avanses()->where('date', $this->date)->where('type', 'Карты')->sum('value');
        if ($cards > 0) {
            $object_ids = $employee->workhours()->where('date', 'LIKE', $this->date . '%')->groupBy('o_id')->pluck('o_id')->toArray();
            if (count($object_ids) > 1) {
                $finance->card_alert = true;
            }
        }

        $finance->is_card = false;
        if ($employee->avanses()->where('type', 'Карты')->count() > 0) {
            $finance->is_card = true;
        }

        return $finance;
    }

    private function fillFinancePaymentInformation($finance, Employee $employee, Object $object, $date)
    {
        $payment = $employee->payments()->where('date', $date)->where('code', $object->code)->first();
        if ($payment) {
            $finance->code = $object->code;
            $finance->paid = $payment->value;
            $finance->paid_date = Carbon::parse($payment->issue_date)->format('d.m.Y H:i');
            $finance->paid_user = $payment->user->name;
        } else {
            $finance->code = $object->code;
            $finance->paid = 0;
            $finance->paid_date = '';
            $finance->paid_user = '';
        }

        if ($this->date == '2017-10' && isset($this->buf_finance)) {
            $finance->buf_paid = $this->buf_finance->total;
        }

        return $finance;
    }

    // Проверка, пустая ли запись (все нули) - нет смысла ее отображать
    private function checkFinance($finance)
    {
        if ($finance->total == 0 && $finance->difference == 0
            && $finance->patent == 0 && $finance->avans == 0
            && $finance->food == 0 && $finance->residence == 0
            && $finance->fine == 0 && $finance->travel == 0
            && $finance->bonus == 0) {

            return false;
        }

        return true;
    }

    // Добавление не хватающих записей на основе помеченых разниц
    private function fillOtherFinances($date, $objects_ids, $employee_ids, $finances)
    {
        if (is_null($objects_ids)) {
            $differences = Difference::where('date', $date)
                ->where('finance_flag', true)
                ->whereIn('e_id', $employee_ids)
                ->get();
            $differences_eids = Difference::where('date', $date)
                ->where('finance_flag', true)
                ->whereIn('e_id', $employee_ids)
                ->groupBy('e_id')->pluck('e_id')->toArray();
            $avanses = Avans::where('date', $date)
                ->where('finance_flag', true)
                ->whereIn('e_id', $employee_ids)
                ->whereNotIn('e_id', $differences_eids)
                ->get();
        } else {
            $object_codes = CObject::whereIn('id', $objects_ids)->pluck('code')->toArray();

            $differences = Difference::where('date', $date)
                ->where('finance_flag', true)
                ->whereIn('code', $object_codes)
                ->get();
            $differences_eids = Difference::where('date', $date)
                ->where('finance_flag', true)
                ->whereIn('code', $object_codes)
                ->groupBy('e_id')->pluck('e_id')->toArray();

            $avanses = Avans::where('date', $date)
                ->where('finance_flag', true)
                ->whereIn('code', $object_codes)
                ->whereNotIn('e_id', $differences_eids)
                ->get();
        }

        foreach ($differences as $difference) {
            $object = CObject::findByCode($difference->code);
            $employee = $difference->employee;

            $finance = (object)[];
            $objects_info = ['codes' => array($object->code), 'ids' => array($object->id)];
            $finance_information = $this->getFinanceInformation($employee, $date, $objects_info, true);
            $finance = $this->fillFinanceFromInformation($finance, $employee, $finance_information);
            $finance = $this->fillFinancePaymentInformation($finance, $employee, $object, $date);

            $check = $this->checkFinance($finance);
            if ($check)
                $finances[] = $finance;
        }

        foreach ($avanses as $avans) {
            $object = CObject::findByCode($avans->code);
            $employee = $avans->employee;

            $finance = (object)[];
            $objects_info = ['codes' => array($object->code), 'ids' => array($object->id)];
            $finance_information = $this->getFinanceInformation($employee, $date, $objects_info, true);
            $finance = $this->fillFinanceFromInformation($finance, $employee, $finance_information);
            $finance = $this->fillFinancePaymentInformation($finance, $employee, $object, $date);

            $check = $this->checkFinance($finance);
            if ($check)
                $finances[] = $finance;
        }

        return $finances;
    }
}
