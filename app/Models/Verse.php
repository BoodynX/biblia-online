<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Verse
 * @package App\Models
 */
class Verse extends Model
{
    /* Eloquent relation definitions */
    public function chapter()       { return $this->belongsTo(Chapter::class); }
    public function verseUserFavs() { return $this->hasMany(VerseUserFav::class); }
}
