<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Chapter extends Model
{
    protected $status;
    protected $is_next;

    /* GETTERS */
    public function getStatus()  : string { return $this->status; }
    public function getIsNext()  : bool { return $this->is_next; }

    /* SETTERS */
    public function setStatus($status) { $this->status = $status; }
    public function setIsNext($is_next) { $this->is_next = $is_next; }


    /* Eloquent relation definitions */
    public function book()                 { return $this->belongsTo(Book::class); }
    public function verses()               { return $this->hasMany(Verse::class); }
    public function chapterFaqs()          { return $this->hasMany(ChapterFaq::class); }
    public function chapterUserQuestions() { return $this->hasMany(ChapterUserQuestions::class); }
    public function planStep()             { return $this->hasOne(PlanStep::class); }

    /**
     * Gets chapter data by its number in a given book
     *
     * @param int $book_no
     * @param int $chapter_no
     * @return self $chapter
     */
    public static function byBookChapter(int $book_no, int $chapter_no) : self
    {
        $conditions = [
            ['book_id', $book_no],
            ['chapter_no', $chapter_no],
        ];
        $with = [
            'chapterFaqs',
            'verses.verseUserFavs' => function ($query) { $query->where('user_id', Auth::id()); }
        ];
        $chapter = self::where($conditions)->with($with)->first();
        return $chapter;
    }
}
