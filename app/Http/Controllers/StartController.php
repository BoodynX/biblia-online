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
        $user_plan_days = UserPlanDay::where('user_id', $user->id)
            ->with('userPlanSteps.planStep.chapter.book')->get();

        foreach ($user_plan_days as $user_plan_day) {
            $userPlanSteps = $user_plan_day->userPlanSteps;
            foreach ($userPlanSteps as $user_plan_step) {
                $chapter = $user_plan_step->planStep->chapter;
                $chapter->setStatus($user_plan_step->status);
                $books[$chapter->book_id] = $chapter->book->title;
                $chapters[$chapter->book_id][$chapter->id] = $chapter;
            }
        }

        return view('start', compact('books', 'chapters'));
    }
}
