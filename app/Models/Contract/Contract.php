<?php

namespace App\Models\Contract;

use App\Models\BankGuarantee;
use App\Models\Company;
use App\Models\Guarantee;
use App\Models\Object\BObject;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Contract extends Model implements HasMedia, Audit
{
    use SoftDeletes, HasUser, HasStatus, InteractsWithMedia, Auditable;

    protected $table = 'contracts';

    protected $fillable = [
        'parent_id', 'type_id', 'company_id', 'object_id', 'created_by_user_id', 'updated_by_user_id',
        'name', 'start_date', 'end_date', 'amount', 'amount_type_id', 'description', 'stage_id', 'status_id',
        'currency', 'currency_rate', 'params'
    ];

    const TYPE_MAIN = 0;
    const TYPE_ADDITIONAL = 1;
    const TYPE_PHASE = 2;
    const TYPE_PRELIMINARY = 3;

    const AMOUNT_TYPE_MAIN = 0;
    const AMOUNT_TYPE_ADDITIONAL = 1;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function bankGuarantees(): HasMany
    {
        return $this->hasMany(BankGuarantee::class, 'contract_id');
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(Guarantee::class, 'contract_id');
    }

    public function acts(): HasMany
    {
        return $this->hasMany(Act::class, 'contract_id');
    }

    public function avanses(): HasMany
    {
        return $this->hasMany(ContractAvans::class, 'contract_id');
    }

    public function avansesReceived(): HasMany
    {
        return $this->hasMany(ContractReceivedAvans::class, 'contract_id');
    }

    public function actPayments(): HasMany
    {
        return $this->hasMany(ActPayment::class, 'contract_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function getStartDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->start_date ? Carbon::parse($this->start_date)->format($format) : '...';
    }

    public function getEndDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format($format) : '...';
    }

    public function isMain(): string
    {
        return $this->type_id === self::TYPE_MAIN;
    }

    public function isMainAmount(): string
    {
        return $this->amount_type_id === self::AMOUNT_TYPE_MAIN;
    }

    public function getAmount(string $currency = null): string|array
    {
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $this->amount : 0;

        if ($this->isMain()) {

            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[7])) {
                return json_decode($this->params)[7] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount = $subContract->isMainAmount()
                    ? $subContract->amount
                    : $amount + $subContract->amount;
            }
        }

        return $amount;
    }

    public function getAvansesAmount(string $currency = null): string|array
    {
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $this->avanses->sum('amount') : 0;
        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && array_key_exists(8, json_decode($this->params) ?? [])) {
                return json_decode($this->params)[8] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->avanses->sum('amount');
            }
        }

        return $amount;
    }

    public function getAvansesReceivedAmount(string $currency = null): string|array
    {
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $this->avansesReceived->sum('amount') : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[9])) {
                return json_decode($this->params)[9] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->avansesReceived->sum('amount');
            }
        }

        return $amount;
    }

    public function getAvansesLeftAmount(string $currency = null): string|array
    {
        $amount = $this->avanses->sum('amount') - $this->avansesReceived->sum('amount');
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && array_key_exists(10, json_decode($this->params) ?? [])) {
                return json_decode($this->params)[10] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->avanses->sum('amount') - $subContract->avansesReceived->sum('amount');
            }
        }

        return $amount;
    }

    public function getActsAmount(string $currency = null): string|array
    {
        $amount = $this->acts->sum('amount');
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && array_key_exists(12, json_decode($this->params) ?? [])) {
                return json_decode($this->params)[12] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->acts->sum('amount');
            }
        }

        return $amount;
    }

    public function getActsAvasesAmount(string $currency = null): string|array
    {
        $amount = $this->acts->sum('amount_avans');
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[13])) {
                return json_decode($this->params)[13] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->acts->sum('amount_avans');
            }
        }

        return $amount;
    }

    public function getActsDepositesAmount(string $currency = null): string|array
    {
        $manualReplaceConfigForGES2 = [
            '1/П-2019/01' => 905725.91,
            '75/П-2017/105/117-ОРСО' => 1807507.62,
            '75/П-2017/118/136-ОРСО' => 1408389.92,
            '75/П-2017/123/166-ОРСО' => 304550.84,
            '75/П-2017/126/233-ОРСО' => 274118.61,
            '75/П-2017/139/219-ОРСО' => 13750.00,
            '75/П-2017/140/210-ОРСО' => 553909.61,
            '75/П-2017/146/231-ОРСО' => 2181818.03,
            '75/П-2017/27/54' => 8880.69,
            '75/П-2017/28/7/233-ОРСО' => 152048.54,
            '75/П-2017/31' => 185566.34,
            '75/П-2017/32' => 17158.57,
            '75/П-2017/4/10/181-ОРСО' => 19598.31,
            '75/П-2017/48/76' => 48638.10,
            '75/П-2017/57/124-ОРСО' => 9058.46,
            '75/П-2017/59/4/232-ОРСО' => 96445.42,
            '75/П-2017/68/193-ОРСО' => 4851.48,
            '75/П-2017/71/95' => 55869.27,
            '75/П-2017/88/96' => 17857.16,
            '435-30-03-16-КТ/68-ПТП' => 632880.14,
            '4/П-2020/02-ОРСО' =>  3854025.13,
        ];

        if ($this->object_id === 5) {
            foreach ($manualReplaceConfigForGES2 as $contractName => $amount) {
                if ($this->name === $contractName) {
                    return $amount;
                }
            }

            return 0;
        }


        $amount = $this->acts->sum('amount_deposit');
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[14])) {
                return json_decode($this->params)[14] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->acts->sum('amount_deposit');
            }
        }

        return $amount;
    }

    public function getActsNeedPaidAmount(string $currency = null): string|array
    {
        $manualReplaceConfigForOktafarmaRUB = [
            '03/2021 от 17.05.21 Фаза 1.0' => ['RUB',19200000],
            '03/2021 от 17.05.21 Фаза 1.1' => ['RUB',14320691.53],
            '03/2021 от 17.05.21 Фаза 1.2' => ['RUB',25844437.15],
            '03/2021 от 17.05.21 Фаза 1.3' => ['RUB',16731334.45],
            '03/2021 от 17.05.21 Фаза 1.4' => ['RUB',27305569.39],
            '03/2021 от 17.05.21 Фаза 1.5' => ['RUB',11604442.91],
            '03/2021 от 17.05.21 Фаза 1.6' => ['RUB',9268884.84],
            '03/2021 от 17.05.21 Фаза 1.7' => ['RUB',16882076.68],
            '03/2021 от 17.05.21 Фаза 1.8' => ['RUB',24664919.61],
            '03/2021 от 17.05.21 Фаза 1.9' => ['RUB',11732209.24],
            '03/2021 от 17.05.21 Фаза 1.10' => ['RUB',6336243.68],
            '03/2021 от 17.05.21 Фаза 1.11' => ['RUB',18841019.36],
            '03/2021 от 17.05.21 Фаза 1.12' => ['RUB',583268.04],
            '03/2021 от 17.05.21 Фаза 1.13' => ['RUB',27462281.21],
            '03/2021 от 17.05.21 Фаза 1.14' => ['RUB',20674132.78],
            '03/2021 от 17.05.21 Фаза 1.15' => ['RUB',38316911.35],
            '03/2021 от 17.05.21 Фаза 1.16' => ['RUB',10015908.37],
            '03/2021 от 17.05.21 Фаза 1.17' => ['RUB',29239519.62],
            '03/2021 от 17.05.21 Фаза 1.18' => ['RUB',36376341.47],
            '03/2021 от 17.05.21 Фаза 1.19' => ['RUB',22491162.41],
            '03/2021 от 17.05.21 Фаза 1.20' => ['RUB',25204901.34],
            '03/2021 от 17.05.21 Фаза 1.21' => ['RUB',2166566.49],
            '03/2021 от 17.05.21 Фаза 1.22' => ['RUB',6302035.6],
            '03/2021 от 17.05.21 Фаза 1.23' => ['RUB',19668536],
            '03/2021 от 17.05.21 Фаза 1.24' => ['RUB',15840755.96],
            '03/2021 от 17.05.21 Фаза 1.25' => ['RUB',31688695.39],
            '03/2021 от 17.05.21 Фаза 1.26' => ['RUB',3997683.49],
            '03/2021 от 17.05.21 Фаза 1.27' => ['RUB',89176778.11],
            '03/2021 от 17.05.21 Фаза 1.28' => ['RUB',1719845.61],
            '03/2021 от 17.05.21 Фаза 1.29' => ['RUB',3015623.76],
            '03/2021 от 17.05.21 Фаза 1.30' => ['RUB',33989847.52],
            '03/2021 от 17.05.21 Д/с 4' => ['RUB',453600],
            '03/2021 от 17.05.21 Д/С 7' => ['RUB', 0],
        ];
        $manualReplaceConfigForOktafarmaEUR = [
            '03/2021 от 17.05.21 Фаза 1.6' => ['EUR',355409.99],
            '03/2021 от 17.05.21 Фаза 1.8' => ['EUR',133241.57],
            '03/2021 от 17.05.21 Фаза 1.9' => ['EUR',791.51],
            '03/2021 от 17.05.21 Фаза 1.10' => ['EUR',151755.22],
            '03/2021 от 17.05.21 Фаза 1.11' => ['EUR',284667.09],
            '03/2021 от 17.05.21 Фаза 1.12' => ['EUR',5009.24],
            '03/2021 от 17.05.21 Фаза 1.16' => ['EUR',326672.49],
            '03/2021 от 17.05.21 Фаза 1.20' => ['EUR',260740.4],
            '03/2021 от 17.05.21 Фаза 1.21' => ['EUR',203919.03],
            '03/2021 от 17.05.21 Фаза 1.22' => ['EUR',267413.99],
            '03/2021 от 17.05.21 Фаза 1.23' => ['EUR',9788.16],
            '03/2021 от 17.05.21 Фаза 1.24' => ['EUR',75308.44],
            '03/2021 от 17.05.21 Фаза 1.26' => ['EUR',315440.46],
        ];

        if ($this->object->code === '346') {
            if ($this->id === 296 && $currency === 'RUB') {
                return 621116223;
            }
            if ($this->id === 296 && $currency === 'EUR') {
                return 2390158;
            }
            foreach ($manualReplaceConfigForOktafarmaRUB as $contractName => $info) {
                $contractFind = mb_strpos($this->name, $contractName . ' ') !== false;

                if ($contractFind && $currency === 'RUB') {
                    return $info[1];
                }
            }

            foreach ($manualReplaceConfigForOktafarmaEUR as $contractName => $info) {
                $contractFind = mb_strpos($this->name, $contractName . ' ') !== false;

                if ($contractFind && $currency === 'EUR') {
                    return $info[1];
                }
            }
        }

        $amount = 0;
        foreach ($this->acts as $act) {
            $amount += $act->getNeedPaidAmount();
        }
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[15])) {
                return json_decode($this->params)[15] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                foreach ($subContract->acts as $act) {
                    $amount += $act->getNeedPaidAmount();
                }
            }
        }

        return $amount;
    }

    public function getActsPaidAmount(string $currency = null): string|array
    {
        $manualReplaceConfigForOktafarmaRUB = [
            '03/2021 от 17.05.21 Фаза 1.0' => ['RUB',19200000],
            '03/2021 от 17.05.21 Фаза 1.1' => ['RUB',14320691.53],
            '03/2021 от 17.05.21 Фаза 1.2' => ['RUB',25844437.15],
            '03/2021 от 17.05.21 Фаза 1.3' => ['RUB',16731334.45],
            '03/2021 от 17.05.21 Фаза 1.4' => ['RUB',27305569.39],
            '03/2021 от 17.05.21 Фаза 1.5' => ['RUB',11604442.91],
            '03/2021 от 17.05.21 Фаза 1.6' => ['RUB',9268884.84],
            '03/2021 от 17.05.21 Фаза 1.7' => ['RUB',16882076.68],
            '03/2021 от 17.05.21 Фаза 1.8' => ['RUB',24664919.61],
            '03/2021 от 17.05.21 Фаза 1.9' => ['RUB',11732209.24],
            '03/2021 от 17.05.21 Фаза 1.10' => ['RUB',6336243.68],
            '03/2021 от 17.05.21 Фаза 1.11' => ['RUB',18841019.36],
            '03/2021 от 17.05.21 Фаза 1.12' => ['RUB',583268.04],
            '03/2021 от 17.05.21 Фаза 1.13' => ['RUB',27462281.21],
            '03/2021 от 17.05.21 Фаза 1.14' => ['RUB',20674132.78],
            '03/2021 от 17.05.21 Фаза 1.15' => ['RUB',38316911.35],
            '03/2021 от 17.05.21 Фаза 1.16' => ['RUB',10015908.37],
            '03/2021 от 17.05.21 Фаза 1.17' => ['RUB',29239519.62],
            '03/2021 от 17.05.21 Фаза 1.18' => ['RUB',36376341.47],
            '03/2021 от 17.05.21 Фаза 1.19' => ['RUB',22491162.41],
            '03/2021 от 17.05.21 Фаза 1.20' => ['RUB',25204901.34],
            '03/2021 от 17.05.21 Фаза 1.21' => ['RUB',2166566.49],
            '03/2021 от 17.05.21 Фаза 1.22' => ['RUB',6302035.6],
            '03/2021 от 17.05.21 Фаза 1.23' => ['RUB',19668536],
            '03/2021 от 17.05.21 Фаза 1.24' => ['RUB',15840755.96],
            '03/2021 от 17.05.21 Фаза 1.25' => ['RUB',31688695.39],
            '03/2021 от 17.05.21 Фаза 1.26' => ['RUB',3997683.49],
            '03/2021 от 17.05.21 Фаза 1.27' => ['RUB',86176777.11],
            '03/2021 от 17.05.21 Фаза 1.28' => ['RUB',1719845.61],
            '03/2021 от 17.05.21 Фаза 1.29' => ['RUB',3015623.76],
            '03/2021 от 17.05.21 Фаза 1.30' => ['RUB',33989847.52],
            '03/2021 от 17.05.21 Д/с 4' => ['RUB',453600],
            '03/2021 от 17.05.21 Д/С 7' => ['RUB', 0],
        ];
        $manualReplaceConfigForOktafarmaEUR = [
            '03/2021 от 17.05.21 Фаза 1.6' => ['EUR',355409.99],
            '03/2021 от 17.05.21 Фаза 1.8' => ['EUR',133241.57],
            '03/2021 от 17.05.21 Фаза 1.9' => ['EUR',791.51],
            '03/2021 от 17.05.21 Фаза 1.10' => ['EUR',151755.22],
            '03/2021 от 17.05.21 Фаза 1.11' => ['EUR',284667.09],
            '03/2021 от 17.05.21 Фаза 1.12' => ['EUR',5009.24],
            '03/2021 от 17.05.21 Фаза 1.16' => ['EUR',326672.49],
            '03/2021 от 17.05.21 Фаза 1.20' => ['EUR',260740.4],
            '03/2021 от 17.05.21 Фаза 1.21' => ['EUR',203919.03],
            '03/2021 от 17.05.21 Фаза 1.22' => ['EUR',267413.99],
            '03/2021 от 17.05.21 Фаза 1.23' => ['EUR',9788.16],
            '03/2021 от 17.05.21 Фаза 1.24' => ['EUR',75308.44],
            '03/2021 от 17.05.21 Фаза 1.26' => ['EUR',315440.46],
        ];

        if ($this->object->code === '346') {
            if ($this->id === 296 && $currency === 'RUB') {
                return 618116222;
            }
            if ($this->id === 296 && $currency === 'EUR') {
                return 2390158;
            }
            foreach ($manualReplaceConfigForOktafarmaRUB as $contractName => $info) {
                $contractFind = mb_strpos($this->name, $contractName . ' ') !== false;

                if ($contractFind && $currency === 'RUB') {
                    return $info[1];
                }
            }

            foreach ($manualReplaceConfigForOktafarmaEUR as $contractName => $info) {
                $contractFind = mb_strpos($this->name, $contractName . ' ') !== false;

                if ($contractFind && $currency === 'EUR') {
                    return $info[1];
                }
            }
        }

        $amount = 0;
        foreach ($this->acts as $act) {
            $amount += $act->payments->sum('amount');
        }
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[16])) {
                return json_decode($this->params)[16] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                foreach ($subContract->acts as $act) {
                    $amount += $act->payments->sum('amount');
                }
            }
        }

        return $amount;
    }

    public function getActsLeftPaidAmount(string $currency = null): string|array
    {
        $manualReplaceConfigForGES2 = [
            '75/П-2017/10' => -966856.55,
            '75/П-2017/118/136-ОРСО' => 1280391.17,
            '75/П-2017/126/233-ОРСО' => 340179.33,
            '75/П-2017/59/4/232-ОРСО' => 220405.93,
            '75/П-2017/68/193-ОРСО' =>-602912.06,
            '4/П-2020/02/1-ОРСО' => 300000.00,
            '09-17-МЗ' => 6000.00,
            '4/П-2020/02-ОРСО' => 11155417.58,
            'ДКС/УЗ/002/2021-ЭА' =>-8393162.91,
        ];

        if ($this->object_id === 5) {
            foreach ($manualReplaceConfigForGES2 as $contractName => $amount) {
                if ($this->name === $contractName) {
                    return $amount;
                }
            }

            return 0;
        }

        $paid = 0;
        foreach ($this->acts as $act) {
            $paid += $act->payments->sum('amount');
        }

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[17])) {
                return json_decode($this->params)[17] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                foreach ($subContract->acts as $act) {
                    $paid += $act->payments->sum('amount');
                }
            }
        }

        $amount = 0;
        foreach ($this->acts as $act) {
            $amount += $act->getNeedPaidAmount();
        }

        if ($this->isMain()) {
            foreach ($this->children->where('currency', $currency) as $subContract) {
                foreach ($subContract->acts as $act) {
                    $amount += $act->getNeedPaidAmount();
                }
            }
        }

        if ($this->object_id === 5 && $currency === 'RUB' && ($amount - $paid) < 0) {
            return 0;
        }

        return $amount - $paid;
    }

    public function getNotworkLeftAmount(string $currency = null): string|array
    {
        $manualReplaceConfigForGES2 = [
            '4/П-2020/02-ОРСО' => 8404489.40
        ];

        if ($this->object_id === 5) {
            foreach ($manualReplaceConfigForGES2 as $contractName => $amount) {
                if ($this->name === $contractName) {
                    return $amount;
                }
            }

            return 0;
        }

        return $this->getAvansesReceivedAmount($currency) - $this->getActsAvasesAmount($currency);
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_MAIN => 'Основной',
            self::TYPE_ADDITIONAL => 'Дополнительный',
            self::TYPE_PHASE => 'Фаза',
            self::TYPE_PRELIMINARY => 'Предварительный'
        ];
    }

    public static function getAmountTypes(): array
    {
        return [
            self::AMOUNT_TYPE_MAIN => 'Основная',
            self::AMOUNT_TYPE_ADDITIONAL => 'Дополнительная',
        ];
    }

    public function getName(): string
    {
        $prefix = match ($this->type_id) {
//            self::TYPE_ADDITIONAL => 'Доп. соглашение ',
//            self::TYPE_PHASE => '',
//            self::TYPE_PRELIMINARY => 'Предв. договор ',
            default => '',
        };

        return $prefix . $this->name;
    }

    public function getType(): string
    {
        return self::getTypes()[$this->type_id];
    }

    public function getAmountType(): string
    {
        return self::getAmountTypes()[$this->amount_type_id];
    }
}
