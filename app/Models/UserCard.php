<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserCard extends Model
{
    protected $table = 'user_card';

    protected $hidden = [
    ];

    public function cardProfit(): BelongsTo
    {
        return $this->belongsTo(CardProfit::class, 'card_profit_id');
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function exchange(): BelongsTo
    {
        return $this->belongsTo(Exchange::class, 'exchange_id');
    }

    protected $appends = [];

    protected $guarded = [];
}
