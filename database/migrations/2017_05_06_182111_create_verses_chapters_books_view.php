<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersesChaptersBooksView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE VIEW verses_chapters_books AS
            SELECT 
                v.id, b.id AS book_id, 
                b.title AS book_title, 
                c.chapter_no, 
                c.title AS chapter_title, 
                v.verse_no AS verse, 
                v.content
            FROM verses v
            LEFT JOIN chapters c ON c.id = v.chapter_id
            LEFT JOIN books b ON b.id = c.book_id
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW `verses_chapters_books`');
    }
}
