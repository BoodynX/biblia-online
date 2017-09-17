<?php

namespace App\Http\Controllers;

use App\Models\UserPlanStep;
use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChaptersController extends Controller
{
    public function show(int $book, int $chapter_no)
    {
        $c = Chapter::byBookChapter($book, $chapter_no);
        $next_c = $this->nextChapter($c->id);
        return view('chapter', compact('c', 'next_c'));
    }

    public function showNext(int $book, int $chapter_no, Request $r)
    {
        $user_id = Auth::id();
        $prev_chapter_id =  intval($r->input('cid'));

        /* Get the users current step id to mark it as read */
        $user_plan_step_id = DB::select('
            SELECT user_plan_steps.id
            FROM chapters
            LEFT JOIN plan_steps ON chapters.id = plan_steps.chapter_id
            LEFT JOIN plan_days ON plan_steps.plan_day_id = plan_days.id
            LEFT JOIN user_plan_days ON plan_days.id = user_plan_days.plan_day_id
            LEFT JOIN user_plan_steps ON user_plan_days.id = user_plan_steps.user_plan_day_id
            WHERE chapters.id = :chapter_id
            AND user_plan_days.user_id = :user_id
            AND plan_steps.id = user_plan_steps.plan_step_id',
            ['chapter_id' => $prev_chapter_id, 'user_id' => $user_id]
        );
        $user_plan_step_id = $user_plan_step_id[0]->id;

        /* Mark the step / chapter as read by this user */
        UserPlanStep::find($user_plan_step_id)->update(['status' => 'done']);

        /* Go to next chapter */
        return $this->show($book, $chapter_no);
    }

    public function index(Book $book)
    {
        return $book->chapters;
    }

    private function nextChapter (int $chapter_id) : \stdClass
    {
        $user_id = Auth::id();
        $conditions = [
            ['user_plan_days.user_id', $user_id],
            ['user_plan_steps.status', 'new']
        ];
        if ($chapter_id) {
            $conditions[] = ['chapters.id', '>', $chapter_id];
        }
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
