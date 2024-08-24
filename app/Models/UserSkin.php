<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkin extends Model
{
    protected $table = 'user_skin';

    protected $hidden = [
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function skin(): BelongsTo
    {
        return $this->belongsTo(Skin::class, 'skin_id');
    }

    protected $appends = [];

    protected $guarded = [];
}
