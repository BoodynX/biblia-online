<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class VerseUserFav extends Model
{
    /* Eloquent relation definitions */
    public function user()  { return $this->belongsTo(User::class); }
    public function verse() { return $this->belongsTo(Verse::class); }
}
