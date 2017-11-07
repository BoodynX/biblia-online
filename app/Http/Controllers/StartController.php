<?php

namespace App\Http\Controllers;

use App\Models\UserPlanDay;
use DB;
use Auth;

class StartController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $books = array();
        $chapters = array();
        $books_with_next_new_chapter = [];
        $user_plan_days = UserPlanDay::where('user_id', $user->id)
            ->with('userPlanSteps.planStep.chapter.book')->get();

        foreach ($user_plan_days as $user_plan_day) {
            $user_plan_steps = $user_plan_day->userPlanSteps;
            foreach ($user_plan_steps as $user_plan_step) {
                $chapter = $user_plan_step->planStep->chapter;
                $chapter->setStatus($user_plan_step->status);
                /* Is this the next chapter in this book? */
                if (!in_array($chapter->book_id, $books_with_next_new_chapter) && $user_plan_step->status === 'new') {
                    $chapter->setIsNext(true);
                    $books_with_next_new_chapter[] = $chapter->book_id;
                    $books[$chapter->book_id]['has_new'] = true;
                } else {
                    $chapter->setIsNext(false);
                }
                /* Build books info array for the view */
                $books[$chapter->book_id]['title'] = $chapter->book->title;
                /* Build chapters info array for the view */
                $chapters[$chapter->book_id][$chapter->id] = $chapter;
            }
        }
        ksort($books);
        $fav_verses = DB::table('verse_user_favs')
            ->leftJoin('verses', 'verses.id', '=', 'verse_user_favs.verse_id')
            ->leftJoin('chapters', 'chapters.id', '=', 'verses.chapter_id')
            ->leftJoin('books', 'books.id', '=', 'chapters.book_id')
            ->select('book_id', 'chapter_no', 'verse_no', 'verse_id', 'content', 'additional_info')
            ->where('user_id', Auth::id())
            ->orderBy('books.id', 'asc')
            ->orderBy('chapters.chapter_no', 'asc')
            ->orderBy('verses.verse_no', 'asc')
            ->get();

        return view('start.start', compact('books', 'chapters', 'fav_verses'));
    }
}
