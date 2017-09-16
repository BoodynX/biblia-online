<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChaptersController extends Controller
{
    public function show(int $book, int $chapter_no, int $chapter_id = null)
    {
        $c = Chapter::byBookChapter($book, $chapter_no);
        $next_c = $this->nextChapter($c->id);
        return view('chapter', compact('c', 'next_c'));
    }

    public function showNext(int $book, int $chapter_no, Request $r)
    {
        /* @TODO mark the last chapter as read ('done') */

        return $this->show($book, $chapter_no, $r->input('cid'));
    }

    public function index(Book $book)
    {
        return $book->chapters;
    }

    private function nextChapter (int $chapter_id)
    {
        $user_id = Auth::id();
        $conditions = [
            ['user_plan_days.user_id', $user_id],
            ['user_plan_steps.status', 'new']
        ];
        $next_c = DB::table('user_plan_days')
            ->leftJoin('user_plan_steps', 'user_plan_days.id', '=', 'user_plan_steps.user_plan_day_id')
            ->leftJoin('plan_steps', 'user_plan_steps.plan_step_id', '=', 'plan_steps.id')
            ->leftJoin('chapters', 'plan_steps.chapter_id', '=', 'chapters.id')
            ->select('chapters.id', 'chapters.chapter_no', 'chapters.book_id','user_plan_steps.status')
            ->where($conditions)
            ->orderBy('chapters.id', 'asc')->take(1)->first();
        return $next_c;
    }
}
