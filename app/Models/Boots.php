<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Boots extends Model
{
    protected $table = 'boots';

    protected $hidden = [
    ];

    public function userBoots(): HasMany
    {
        return $this->hasMany(UserBoots::class, 'boots_id');
    }

    protected $appends = [];

    protected $guarded = [];
}
