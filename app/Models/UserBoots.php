<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBoots extends Model
{
    protected $table = 'user_boots';

    protected $hidden = [
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function boots(): BelongsTo
    {
        return $this->belongsTo(Boots::class, 'boots_id');
    }

    protected $appends = [];

    protected $guarded = [];
}
