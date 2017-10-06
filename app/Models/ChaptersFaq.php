<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChaptersFaq extends Model
{
    /* Eloquent relation definitions */
    public function chapter()   { return $this->belongsTo(Chapter::class); }
}
