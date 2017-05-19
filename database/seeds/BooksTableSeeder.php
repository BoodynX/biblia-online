<?php

use Illuminate\Database\Seeder;

class BooksTableSeeder extends Seeder
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
                'id'    => 1,
                'title' => 'Title 1',
            ],
            [
                'id'    => 2,
                'title' => 'Title 2',
            ],
        ];
        foreach ($data as $row) {
            DB::table('books')->insert([
                'title' => $row['title']
            ]);
        }
    }
}
