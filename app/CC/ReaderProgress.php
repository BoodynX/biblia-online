<?php

namespace App\CC;

use App\Models\UserPlanDay;
use App\Models\UserPlanStep;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\User;

/**
 * Class ReaderProgress is responsible for realizing the users reading plan.
 * @package App\CC
 */
class ReaderProgress
{
    /** @const Max number of days that can be unlocked at once */
    const MAX_DAYS = 7;

    /**
     * Checks how many days the user was absent and how many days he has available for unlocking.
     * Unlocks the calculated number of days (and steps) and updates last login date in Users table.
     */
    public static function checkAndUpdate()
    {
        $user       = Auth::user();
        $last_login = $user->last_login;
        $user_id    = $user->id;
        $last_login_carbon = Carbon::parse($last_login);
        $this_login_carbon = Carbon::now();
        /* Check how long (in days) the user was gone. */
        $days_since_last_login = $last_login_carbon->diffInDays($this_login_carbon);
        /* Check how many "unread days" the user has left, before unlocking anymore chapters. */
        $user_plan_days = UserPlanDay::where([['user_id', $user_id],['status','new']])->with('userPlanSteps')->get();
        $days_not_complete = self::countIncompleteDays($user_plan_days);
        /**
         * Check if user did not log in today and if he didn't reach MAX_DAYS available but not read.
         * If not than unlock appropriate number of days (and steps) to read.
         */
        if ($days_since_last_login > 0 && $days_not_complete < self::MAX_DAYS) {
            /* Establish how many days can be unlocked without crossing the limit.  */
            if (($days_since_last_login + $days_not_complete) <= self::MAX_DAYS) {
                $days_to_unlock = $days_since_last_login;
            } else {
                $days_to_unlock = self::MAX_DAYS - $days_not_complete;
            }
            /* Unlock established number of days. */
            self::unlock($user_id, $days_to_unlock);
        }
        /* Update users last login to current date. */
        $this_login = $this_login_carbon->toDateString();
        User::where('id', $user_id)->update(['last_login' => $this_login]);
    }

    /**
     * Unlocks a given number of days (and steps) to read for a given user id.
     *
     * @param int $user_id
     * @param int $days_to_unlock
     */
    public static function unlock(int $user_id, int $days_to_unlock)
    {
        /* Add user plan days. */
        $last_day    = UserPlanDay::where('user_id', $user_id)->max('plan_day_id');
        $next_day    = $last_day + 1;
        $days_range  = range($next_day , $last_day + $days_to_unlock);
        $created_at  = Carbon::now()->toDateTimeString();
        $days_to_add = array_map(
            function ($day) use ($user_id, $created_at) {
                return  ['user_id' => $user_id, 'plan_day_id' => $day, 'created_at'=> $created_at];
            },
            $days_range
        );
        $upd_insert_status = UserPlanDay::insert($days_to_add);
        /* Add user plan steps. */
        if ($upd_insert_status) {
            /* Get the plan schema. */
            $user_plan_days = UserPlanDay::where('user_id', $user_id)
                ->orderBy('plan_day_id', 'desc')
                ->take($days_to_unlock)
                ->with('planDay.planSteps')
                ->get();
            $user_plan_days = $user_plan_days->sortBy('plan_day_id')->values();
            /* Build array with plan steps. */
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
            /* Insert plan steps. */
            UserPlanStep::insert($steps_to_add);
        }
    }

    /**
     * Count how many unlocked days the user has not completed yet.
     *
     * @param  Collection  $user_plan_days  A collection of specific users unlocked days.
     * @return int
     */
    private static function countIncompleteDays(Collection $user_plan_days) : int
    {
        $days_not_complete = 0;
        foreach ($user_plan_days as $upd) {
            $user_plan_steps = $upd->userPlanSteps;
            if (! self::checkDayStepsStatus($user_plan_steps)) {
                $days_not_complete++;
            }
        }
        return $days_not_complete;
    }

    /**
     * Checks status of each step in a given collection.
     * Returns true only if all the steps are 'done'.
     *
     * @param Collection  $user_plan_steps  A collection of specific users
     * @return bool
     */
    private static function checkDayStepsStatus(Collection $user_plan_steps) : bool
    {
        $all_day_steps_done = true;
        foreach ($user_plan_steps as $step) {
            if ($step->status !== 'done') {
                $all_day_steps_done = false;
                break;
            }
        }
        return $all_day_steps_done;
    }
}