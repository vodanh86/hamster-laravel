<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitPerHour extends Model
{
    protected $table = 'profit_per_hours';

	protected $hidden = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exchange()
    {
        return $this->belongsTo(Exchange::class, 'exchange_id');
    }

	protected $guarded = [];
}
