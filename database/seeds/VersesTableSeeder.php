<?php

use Illuminate\Database\Seeder;
//use Carbon\Carbon;

class VersesTableSeeder extends Seeder
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
                'verse_no'   => 1,
                'chapter_id' => 1,
                'body'       => 'Verse 1 Chapter 1 Book 1',
                /*'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),*/
            ],
            [
                'id'         => 2,
                'verse_no'   => 2,
                'chapter_id' => 1,
                'body'       => 'Verse 2 Chapter 1 Book 1',
                /*'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),*/
            ],
            [
                'id'         => 3,
                'verse_no'   => 1,
                'chapter_id' => 2,
                'body'       => 'Verse 1 Chapter 2 Book 1',
                /*'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),*/
            ],
            [
                'id'         => 4,
                'verse_no'   => 2,
                'chapter_id' => 2,
                'body'       => 'Verse 2 Chapter 2 Book 1',
                /*'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),*/
            ],
            [
                'id'         => 5,
                'verse_no'   => 1,
                'chapter_id' => 3,
                'body'       => 'Verse 1 Chapter 1 Book 2',
                /*'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),*/
            ],
            [
                'id'         => 6,
                'verse_no'   => 2,
                'chapter_id' => 3,
                'body'       => 'Verse 2 Chapter 1 Book 2',
                /*'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),*/
            ],
            [
                'id'         => 7,
                'verse_no'   => 1,
                'chapter_id' => 4,
                'body'       => 'Verse 1 Chapter 2 Book 2',
                /*'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),*/
            ],
            [
                'id'         => 8,
                'verse_no'   => 2,
                'chapter_id' => 4,
                'body'       => 'Verse 2 Chapter 2 Book 2',
                /*'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),*/
            ],
        ];

        foreach ($data as $row) {
            DB::table('verses')->insert([
                'id'         => $row['id'],
                'verse_no'   => $row['verse_no'],
                'chapter_id' => $row['chapter_id'],
                'body'       => $row['body']
            ]);
        }
    }
}
