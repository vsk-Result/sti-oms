<?php

namespace App\Models\Schema;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;


class Interaction extends Model implements Audit
{
    use HasStatus, HasUser, Auditable;

    protected $table = 'schema_interactions';

    protected $fillable = ['name', 'currency', 'amount', 'status_id', 'created_by_user_id', 'updated_by_user_id'];

    public static function getNames(): array
    {
        return [
            'СТИ-Срджан_Премия',
            'СТИ-Энес_Премия',
            'СТИ_НП',
            'СТИ-ПТИ_Лизинг',
            'СТИ-ПТИ_Поставка',
            'СТИ-БАМС_Аренда офиса и склада',
            'СТИ-ДТГ_Поставка',
            'СТИ-ДТГ_Займ',
            'СТИ-Milenium_Переступка',
            'СТИ-Belenzia_Поставка',
            'ПТИ-СТИ_Займ',
            'ПТИ_НП',
            'ПТИ-ДТГ_Поставка',
            'ПТИ-ДТГ_Займ',
            'ПТИ-БАМС_Аренда офиса',
            'ДТГ-ПТИ_Лизинг',
            'ДТГ_НП',
            'ДТГ-Любомир_Премия',
            'ДТГ-БАМС_Аренда офиса и склада',
            'ДТГ-СТИ_Ретро',
            'ДТГ-СТИ_Займ',
            'ДТГ-Milenium_Переступка',
            'ДТГ-Belenzia_Поставка',
            'ДТГ-Belenzia_Поставка',
            'ДТГ-NS_Поставка',
            'ДТГ-Maviboni_Поставка',
            'БАМС_НП',
            'БАМС-СТИ_Займ',
            'БАМС-ДТГ_Займ',
            'NS-Maviboni_Поставка',
            'Maviboni-NS_Поставка',
            'Maviboni-Прохорова_%',
            'Maviboni_НП',
            'Belenzia-Maviboni_Переступка',
        ];
    }

    public static function getCompanies(): array
    {
        return ['Maviboni', 'БАМС', 'Belenzia', 'ДТГ', 'СТИ', 'ПТИ', 'NS', 'Milenium'];
    }
}
