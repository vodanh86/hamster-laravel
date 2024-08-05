<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserFriend extends Model
{
    protected $table = 'user_friend';

    protected $hidden = [
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reference(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reference_id');
    }

    protected $appends = [];

    protected $guarded = [];
}
