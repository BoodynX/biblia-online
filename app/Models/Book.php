<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /* Eloquent relation definitions */
    public function chapters() { return $this->hasMany(Chapter::class); }
}
