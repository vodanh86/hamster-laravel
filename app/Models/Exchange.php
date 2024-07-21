<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $table = 'exchanges';

	protected $hidden = [
    ];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute($value)
    {
        return env('APP_URL'). "/storage/" . $this->image;
    }

	protected $guarded = [];
}
