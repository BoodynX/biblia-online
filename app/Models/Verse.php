<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Collection;

/**
 * Class Verse
 * @package App\Models
 */
class Verse extends Model
{
    /**
     * Method for fetching bible parts by natural indexes
     * @TODO make separate views for separate language versions
     *
     * @param int $book
     * @param int|null $chapter
     * @param int|null $verse
     * @return \Illuminate\Support\Collection
     */
    public static function byBookChapterVerse(int $book, int $chapter = null, int $verse = null) : Collection
    {
        $conditions = [
            ['book_id', $book],
            ['chapter_no', $chapter],
            ['verse', $verse],
        ];
        return DB::table('verses_chapters_books')->where($conditions)->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
