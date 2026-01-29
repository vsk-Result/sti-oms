<?php

namespace App\Models;

use App\Models\CashAccount\CashAccount;
use App\Models\Object\BObject;
use App\Traits\HasStatus;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasRoles, HasStatus, SoftDeletes, AuthenticationLoggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'photo',
        'phone',
        'email',
        'status_id',
        'password',
        'email_verified_at',
        'crm_user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function objects(): BelongsToMany
    {
        return $this->belongsToMany(BObject::class, 'object_user', 'object_id', 'user_id');
    }

    public function sharedCashAccounts(): BelongsToMany
    {
        return $this->belongsToMany(CashAccount::class, 'cash_account_user', 'cash_account_id', 'user_id');
    }

    public function getPhoto(): string
    {
        return $this->photo ? "/storage/$this->photo" : asset('images/blanks/user_avatar_blank.png');
    }

    public function favouriteLinks(): HasMany
    {
        return $this->hasMany(FavouriteLink::class, 'created_by_user_id')->orderBy('order');
    }

    public function getInitials(): string
    {
        return implode('', array_map(function($s) {
            return mb_strtoupper(mb_substr($s, 0, 1));
        }, explode(' ', $this->name)));
    }

    public function getInitialColor(): string
    {
        $firstChar = mb_strtoupper(mb_substr(explode(' ', $this->name)[0], 0, 1));

        if ($firstChar < 'Д') {
            return 'primary';
        }

        if ($firstChar < 'И') {
            return 'warning';
        }

        if ($firstChar < 'О') {
            return 'danger';
        }

        if ($firstChar < 'У') {
            return 'info';
        }

        if ($firstChar < 'Ш') {
            return 'secondary';
        }

        return 'dark';
    }
}
