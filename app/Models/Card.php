<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    protected $table = 'card';

    protected $hidden = [
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function cardProfits(): HasMany
    {
        return $this->hasMany(CardProfit::class, 'card_id');
    }


    protected $appends = [];

    protected $guarded = [];
}
