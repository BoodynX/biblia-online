<?php

namespace App\Http\Controllers;

use App\Models\VerseUserFav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class VersesController extends Controller
{
    public function storeFav(Request $request)
    {
        $fav_verse = VerseUserFav::where([
            ['verse_id', $request->verse_id],
            ['user_id' , Auth::id()]
        ])->first();

        if ($fav_verse === null) {
            $new_fav_verse = new VerseUserFav;
            $new_fav_verse->verse_id = $request->verse_id;
            $new_fav_verse->user_id  = Auth::id();
            $new_fav_verse->save();
            $return = Config::get('commons.buttons.rem_from_favs');
        } else {
            $fav_verse->delete();
            $return = Config::get('commons.buttons.add_to_favs');
        }
        return $return;
    }
}
