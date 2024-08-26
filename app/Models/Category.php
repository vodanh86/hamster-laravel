<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';

    protected $hidden = [
    ];

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
    protected $appends = [];

    protected $guarded = [];
}
