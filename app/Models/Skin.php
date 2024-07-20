<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skin extends Model
{
    protected $table = 'skins';

	protected $hidden = [
    ];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute($value)
    {
        return env('APP_URL'). "/storage/" . $this->image;
    }

	protected $guarded = [];
}
