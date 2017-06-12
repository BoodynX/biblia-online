<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Chapter extends Model
{
    protected $verses;
    protected $title;

    /* GETTERS */
    public function getTitle() : string { return $this->title; }
    public function getVerses() : array { return $this->verses; }

    /* Eloquent relation definitions */
    public function book() { return $this->belongsTo(Book::class); }
    public function verses() { return $this->hasMany(Verse::class); }

    /**
     * Gets chapter data by its number in a given book
     *
     * @param int $book
     * @param int $chapter_no
     * @return $this
     */
    public function byBookChapter(int $book, int $chapter_no) : self
    {
        $conditions[] = ['book_id', $book];
        $conditions[] = ['chapter_no', $chapter_no];
        $chapterCollection = DB::table('verses_chapters_books')->where($conditions);
        $this->verses = $chapterCollection->pluck('content')->all();
        $this->title  = $chapterCollection->first()->chapter_title;
        return $this;
    }
}
