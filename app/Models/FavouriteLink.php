<?php

namespace App\Models;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FavouriteLink extends Model
{
    use SoftDeletes, HasUser, HasStatus;

    protected $table = 'favourite_links';

    protected $fillable = ['name', 'link', 'order', 'created_by_user_id', 'updated_by_user_id', 'status_id'];

    public static function getNextOrder(): int
    {
        return auth()->user()->favouriteLinks()->first()->order ?? 1;
    }
}
