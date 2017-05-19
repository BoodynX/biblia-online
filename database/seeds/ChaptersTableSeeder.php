<?php

use Illuminate\Database\Seeder;

class ChaptersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id'         => 1,
                'chapter_no' => 1,
                'book_id'    => 1,
                'title'      => 'Chap 1 Book 1'
            ],
            [
                'id'         => 2,
                'chapter_no' => 2,
                'book_id'    => 1,
                'title'      => 'Chap 2 Book 1'
            ],
            [
                'id'         => 3,
                'chapter_no' => 1,
                'book_id'    => 2,
                'title'      => 'Chap 1 Book 2'
            ],
            [
                'id'         => 4,
                'chapter_no' => 2,
                'book_id'    => 2,
                'title'      => 'Chap 2 Book 2'
            ],
        ];

        foreach ($data as $row) {
            DB::table('chapters')->insert([
                'id'         => $row['id'],
                'chapter_no' => $row['chapter_no'],
                'book_id'    => $row['book_id'],
                'title'      => $row['title']
            ]);
        }
    }
}
