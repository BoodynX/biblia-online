<?php

namespace App\CC;

use DB;
use Auth;
use stdClass;
use App\User;
use App\Models\UserPlanDay;
use App\Models\UserPlanStep;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

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
     * Get the next step after given chapter or a given book.
     * If you want to get the first step in the next book submit $chapter_id = false and $book_no of current book.
     * If you want to get users next step in general submit $chapter_id = false and do not submit $book_no.
     *
     * @param  int|bool  $chapter_id     Next after this chapter id.
     * @param  int|bool  $book_no        Next after this book id/no.
     * @return stdClass
     */
    public static function nextStep($chapter_id, $book_no = false) : stdClass
    {
        $user_id = Auth::id();
        /**
         * Check if there are any new steps left_to_read.
         */
        $conditions = [
            ['user_plan_steps.status', 'new'],
            ['user_id', $user_id]
        ];
        $left_to_read = DB::table('user_plan_days')
            ->leftJoin('user_plan_steps', 'user_plan_days.id', '=', 'user_plan_steps.user_plan_day_id')
            ->select('user_plan_steps.status')
            ->where($conditions)->count();

        if ($left_to_read < 2) {
            /**
             * If there are no unread chapters or only one is left, HIDE the 'next step' and 'next chapter' navigation
             * buttons and show a 'Start' button.
             */
            $next_step = new stdClass();
            $next_step->loop    = false;
            $next_step->the_end = true;
        } else {
            /**
             * If there are more steps to read find the next one after current chapter one.
             */
            $conditions = [
                ['user_plan_days.user_id', $user_id],
                ['user_plan_steps.status', 'new'],
                ['chapters.id', '>', $chapter_id]
            ];
            if ($book_no !== false) {
                $conditions[] = ['chapters.book_id', '>', $book_no];
            }
            $next_step = self::getUserPlanStepsAccordingTo($conditions, true);

            if ($next_step === null) {
                /**
                 * If there is no unread chapters after the current one:
                 * - find the first unread step/chapter in general,
                 * - LOOP BACK to it with a dedicated button,
                 * - hide the 'next' button/s.
                 */
                $conditions = [
                    ['user_plan_days.user_id', $user_id],
                    ['user_plan_steps.status', 'new']
                ];
                $next_step = self::getUserPlanStepsAccordingTo($conditions, true);
                $next_step_prototype = new stdClass();
                $next_step_prototype->loop = true;
                $next_step_prototype->chapter_no = $next_step->chapter_no;
                $next_step_prototype->book_id = $next_step->book_id;
                $next_step = $next_step_prototype;
            } else {
                /**
                 * If there is more don't loop back, show the 'next' button/s.
                 */
                $next_step->loop = false;
            }
            /**
             * If its not the end, let them know :)
             */
            $next_step->the_end = false;
        }
        return $next_step;
    }

    /**
     * Mark current step/chapter as read.
     *
     * @param int $chapter_id
     */
    public static function markStepAsRead(int $chapter_id)
    {
        $user_id = Auth::id();
        $prev_chapter_id = intval($chapter_id);

        /* Get the users current step id to mark it as read. */
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

        /* Mark the step / chapter as read by this user. */
        UserPlanStep::find($user_plan_step_id)->update(['status' => 'done']);
    }

    /**
     * Check if chapter is available for the user.
     *
     * @param  int $book_no
     * @param  int $chapter_no
     * @return bool
     */
    public static function isChapterUnlocked(int $book_no, int $chapter_no)
    {
        $user_id = Auth::id();
        /* Assume chapter is locked. */
        $access = false;
        /* Get list of all chapters from the requested book, available for the user to read or unlock today. */
        $conditions = [
            ['user_plan_days.user_id', $user_id],
            ['chapters.book_id', $book_no]
        ];
        $user_plan_steps = self::getUserPlanStepsAccordingTo($conditions);
        /**
         * Check if the chapter is on the list and if it was already read or is the first new one in the book.
         * - if the chapter is on the list with status other than "new" access is granted,
         * - if the chapter is the first on the list with status "new" access is granted,
         * - if the chapter is not the first on the list with status "new" access is denied,
         * - if the chapter not on the list the assumption $access = false remains and access is denied.
         */
        foreach ($user_plan_steps as $ups) {
            if ($ups->chapter_no == $chapter_no) {
                /* Make chapter available and break the loop. */
                $access = true;
                break;
            } elseif ($ups->status == 'new') {
                /* Make chapter available and break the loop. */
                break;
            }
        }
        return $access;
    }

    /**
     * Fetch user plan steps according to given conditions.
     *
     * @param  array $conditions  Conditions according to witch the steps will be filtered.
     * @param  bool  $get_first   If true only the first plan step will be returned.
     * @return mixed
     */
    private static function getUserPlanStepsAccordingTo(array $conditions, bool $get_first = false)
    {
        if ($get_first){
            $fetch = 'first';
        } else {
            $fetch = 'get';
        }
        return DB::table('user_plan_days')
            ->leftJoin('user_plan_steps', 'user_plan_days.id', '=', 'user_plan_steps.user_plan_day_id')
            ->leftJoin('plan_steps', 'user_plan_steps.plan_step_id', '=', 'plan_steps.id')
            ->leftJoin('chapters', 'plan_steps.chapter_id', '=', 'chapters.id')
            ->select('chapters.id', 'chapters.chapter_no', 'chapters.book_id','user_plan_steps.status')
            ->where($conditions)
            ->orderBy('chapters.id', 'asc')->$fetch();
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