<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    /* GETTERS */
    public function getContent() : array { return $this->verses->pluck('content')->all(); }

    /* Eloquent relation definitions */
    public function book()   { return $this->belongsTo(Book::class); }
    public function verses() { return $this->hasMany(Verse::class); }
    public function planStep(){ return $this->hasOne(PlanStep::class); }

    /**
     * Gets chapter data by its number in a given book
     *
     * @param int $book_no
     * @param int $chapter_no
     * @return $chapter
     */
    public static function byBookChapter(int $book_no, int $chapter_no)
    {
        $conditions = [
            ['book_id', $book_no],
            ['chapter_no', $chapter_no],
        ];
        $chapter = self::where($conditions)->first();
        return $chapter;
    }
}
