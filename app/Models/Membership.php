<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    protected $table = 'memberships';

	protected $hidden = [
    ];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute($value)
    {
        return env('APP_URL'). "/storage/" . $this->image;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'membership_id');
    }

	protected $guarded = [];
}
