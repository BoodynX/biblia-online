<?php

namespace App\CC;

use App\Models\UserPlanDay;
use App\Models\UserPlanStep;
use App\Models\PlanStep;
use App\Models\PlanDay;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\User;

class ReaderProgress
{
    /* Max number of days that can be unlocked at once */
    protected const MAX_DAYS = 7;

    public static function checkAndUpdate()
    {
        $user = Auth::user();
        $last_login = $user->last_login;
        $last_login_carbon = Carbon::parse($last_login);
        $this_login_carbon = Carbon::now();
        $days_since_last_login = $last_login_carbon->diffInDays($this_login_carbon);
        $user_id = $user->id;
        /* Check credits and unlock appropriate number of "days to read"  */
        if($days_since_last_login > 0) {
            /* Max days that can be unlocked is defined by MAX_DAYS const */
            $days_since_last_login = $days_since_last_login > self::MAX_DAYS ? self::MAX_DAYS : $days_since_last_login;

            /* Check how many "unread days" the user has, before unlocking anymore chapters */
            $user_plan_days = UserPlanDay::where([['user_id', $user_id],['status','new']])->with('userPlanSteps')->get();
            $days_not_compleat = self::checkDaysStatus($user_plan_days);
            $days_to_unlock = $days_since_last_login - $days_not_compleat;
            if ($days_to_unlock > 0) {
                self::update($user_id, $days_to_unlock);
            }
        }
        /*update users last login to current date*/
        $this_login = $this_login_carbon->toDateString();
        User::where('id', $user_id)->update(['last_login' => $this_login]);
    }

    public static function update(int $user_id, int $days_to_unlock)
    {
        /* Add user plan days */
        $last_day = UserPlanDay::where('user_id', $user_id)->max('plan_day_id');
        $next_day = $last_day + 1;
        $days_range = range($next_day , $last_day + $days_to_unlock);
        $created_at = Carbon::now()->toDateTimeString();
        $days_to_add = array_map(function ($day) use ($user_id, $created_at) {
            return  ['user_id' => $user_id, 'plan_day_id' => $day, 'created_at'=> $created_at];
        }, $days_range);
        $upd_insert_status = UserPlanDay::insert($days_to_add);
        /* Add user plan steps */
        if ($upd_insert_status) {
            /* Get the plan schema */
            $user_plan_days = UserPlanDay::where('user_id', $user_id)
                ->orderBy('plan_day_id', 'desc')
                ->take($days_to_unlock)
                ->with('planDay.planSteps')
                ->get();
            $user_plan_days = $user_plan_days->sortBy('plan_day_id')->values();
            /* Build array with plan steps */
            $steps_to_add = [];
            foreach ($user_plan_days as $upd) {
                $plan_steps = $upd->planDay->planSteps;
                foreach ($plan_steps as $ps) {
                    $steps_to_add[] = [
                        'plan_step_id' => $ps->id,
                        'user_plan_day_id' => $upd->id,
                        'created_at'=> $created_at
                    ];
                }
            }
            /* Insert plan steps */
            UserPlanStep::insert($steps_to_add);
        }
    }

    private static function checkDaysStatus(Collection $user_plan_days) : int
    {
        $days_not_compleat = 0;
        foreach ($user_plan_days as $upd) {
            $user_plan_steps = $upd->userPlanSteps;
            if (! self::checkDayStepsStatus($user_plan_steps)) {
                $days_not_compleat++;
            }
        }
        return $days_not_compleat;
    }

    private static function checkDayStepsStatus(Collection $user_plan_steps) : bool
    {
        $step_done = true;
        foreach ($user_plan_steps as $step) {
            if ($step->status !== 'done') {
                $step_done = false;
                break;
            }
        }
        return $step_done;
    }
}