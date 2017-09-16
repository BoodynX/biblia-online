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
        $user_plan_days = UserPlanDay::where('user_id', $user->id)
            ->with('planDay.planSteps.chapter')->get();

        foreach ($user_plan_days as $user_plan_day) {
            $plan_steps = $user_plan_day->planDay->planSteps;
            foreach ($plan_steps as $plan_step) {
                $chapter = $plan_step->chapter;
                $chapters[$chapter->book_id][$chapter->id] = $chapter;
            }
        }

        return view('start', compact('chapters'));
    }
}
