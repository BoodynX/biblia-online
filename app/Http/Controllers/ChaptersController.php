<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Book;

class ChaptersController extends Controller
{
    public function show(int $book, int $chapter_no)
    {
        $c = Chapter::byBookChapter($book, $chapter_no);
        return view('chapter', compact('c'));
    }

    public function index(Book $book)
    {
        return $book->chapters;
    }
}
