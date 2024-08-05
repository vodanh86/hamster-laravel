<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserEarn extends Model
{
    protected $table = 'user_earn';

    protected $hidden = [
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function earn(): BelongsTo
    {
        return $this->belongsTo(Earn::class, 'earn_id');
    }

    protected $appends = [];

    protected $guarded = [];
}
