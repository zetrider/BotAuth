<?php

namespace ZetRider\BotAuth\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class BotAuth extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider',
        'external_id',
        'secret',
    ];

    /**
     * User
     * @return App\User
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
