<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Earn extends Model
{
    protected $table = 'earn';

    protected $hidden = [
    ];

    public function userEarn(): HasMany
    {
        return $this->hasMany(UserEarn::class, 'earn_id');
    }

    protected $appends = [];

    protected $guarded = [];
}
