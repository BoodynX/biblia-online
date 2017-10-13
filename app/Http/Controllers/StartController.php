<?php

namespace App\Http\Controllers;

use App\Models\UserPlanDay;
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
                } else {
                    $chapter->setIsNext(false);
                }
                /* Build books info array for the view */
                $books[$chapter->book_id] = $chapter->book->title;
                /* Build chapters info array for the view */
                $chapters[$chapter->book_id][$chapter->id] = $chapter;
            }
        }
        return view('start', compact('books', 'chapters'));
    }
}
