<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Verse;
use App\Models\Chapter;

class VersesController extends Controller
{
    public function show(int $book, int $chapter, int $verse)
    {
        return Verse::byBookChapterVerse($book, $chapter, $verse);
    }
}
