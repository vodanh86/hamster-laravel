<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardProfit extends Model
{
    protected $table = 'card_profit';

    protected $hidden = [
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
    public function requiredCard(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'required_card');
    }

    protected $appends = [];

    protected $guarded = [];
}