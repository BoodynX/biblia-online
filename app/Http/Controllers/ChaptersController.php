<?php

namespace App\Http\Controllers;

use App\Models\UserPlanStep;
use Illuminate\Http\Request;
use App\Models\Chapter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ChapterUserQuestion;
use stdClass;

class ChaptersController extends Controller
{
    const LAST_BOOK = 73;
    /**
     * @TODO make:
     * - DocBlocks for all methods
     */

    public function show(int $book_no, int $chapter_no)
    {
        /* Check if user unlocked this chapter */
        if (! $this->isChapterUnlocked($book_no, $chapter_no)) {
            return redirect()->route('start');
        }
        /* Get chapter data */
        $chapter   = Chapter::byBookChapter($book_no, $chapter_no);
        /* Get next step in general */
        $next_step = $this->nextStep($chapter->id);
        /* Get next step in next book */
        $next_book = $this->nextStep(false, $chapter->book_id);
        return view('chapter.chapter', compact('chapter', 'next_step', 'next_book'));
    }

    public function showNext(int $book_no, int $chapter_no, Request $r)
    {
        /* Mark current user step as read */
        $this->currentUserStepRead($r);
        /* Go to next chapter */
        return $this->show($book_no, $chapter_no);
    }

    public function showEnd(Request $r)
    {
        /* Mark current user step as read */
        $this->currentUserStepRead($r);
        /* Go to end screen */
        return redirect('start');
    }

    /**
     * This method redirects users to the first unread reading step in the plan. It is used in routing
     * @see routes/web.php - Route::get('/nastepny_krok', 'ChaptersController(at)findNextStep');
     */
    public function findNextStep()
    {
        /* Find next step */
        $next_chapter = $this->nextStep(false);
        if ($next_chapter->the_end) {
            /* If there are now chapters to be read redirect to start */
            $return = redirect('start');
        } else {
            /* Redirect to next step */
            $return = redirect('ksiega/'.$next_chapter->book_id.'/rozdzial/'.$next_chapter->chapter_no);
        }
        return $return;
    }

    public function storeQuestion(Request $request, ChapterUserQuestion $question)
    {
        $question->user_id    = Auth::id();
        $question->chapter_id = $request->cid;
        $question->question   = $request->user_question;
        $query_status = $question->save();
        if ($query_status === true) {
            $response = 'Dziękujemy za wysłane pytanie. Odpowiemy możliwie jak najszybciej.';
        } else {
            $response = 'Wystąpiły problemy techniczne. Prosimy spróbować później.';
        }
        return $response;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// PRIVATES
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param int       $chapter_id     Next after this chapter id
     * @param int|bool  $book_no        Next after this book id/no
     * @return stdClass
     */
    private function nextStep($chapter_id, $book_no = false) : stdClass
    {
        $user_id = Auth::id();
        /**
         * Check if there are any new steps left_to_read
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
             * buttons and show a 'Start' button
             */
            $next_step = new stdClass();
            $next_step->loop    = false;
            $next_step->the_end = true;
        } else {
            /**
             * If there are more steps to read find the next one after current chapter one
             */
            $conditions = [
                ['user_plan_days.user_id', $user_id],
                ['user_plan_steps.status', 'new'],
                ['chapters.id', '>', $chapter_id]
            ];
            if ($book_no !== false) {
                $conditions[] = ['chapters.book_id', '>', $book_no];
            }
            $next_step = $this->getFirstUserPlanStepAccordingTo($conditions);

            if ($next_step === null) {
                /**
                 * If there is no unread chapters after the current one, find the first unread in general, and LOOP BACK to it
                 * with a dedicated button and hide the 'next' buttons
                 */
                $conditions = [
                    ['user_plan_days.user_id', $user_id],
                    ['user_plan_steps.status', 'new']
                ];
                $next_step = $this->getFirstUserPlanStepAccordingTo($conditions);
                $next_step_prototype = new stdClass();
                $next_step_prototype->loop = true;
                $next_step_prototype->chapter_no = $next_step->chapter_no;
                $next_step_prototype->book_id = $next_step->book_id;
                $next_step = $next_step_prototype;
            } else {
                /**
                 * If there is more don't loop back, show the 'next' button/s
                 */
                $next_step->loop = false;
            }
            /**
             * If its not the end let them know :)
             */
            $next_step->the_end = false;
        }
        return $next_step;
    }

    private function currentUserStepRead(Request $r)
    {
        $user_id = Auth::id();
        $prev_chapter_id = intval($r->input('cid'));

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
    }

    private function isChapterUnlocked($book_no, $chapter_no)
    {
        /**
         * @TODO just make loop through users steps in this chapter and see
         * - if its 'done'
         * - or the first 'new'
         * - else no access
         */

        $user_id = Auth::id();
        /**
         * Get the FIRST new user_plan_step for given chapter
         */
        $conditions = [
            ['user_plan_days.user_id', $user_id],
            ['user_plan_steps.status', 'new'],
            ['chapters.book_id', $book_no]
        ];
        $first_new_step = $this->getFirstUserPlanStepAccordingTo($conditions);

        if ($first_new_step !== null) {
            /**
             * If there are 'new' steps to read see if the requested chapter is the first one of them
             */
            if ($first_new_step->chapter_no >= $chapter_no) {
                $return = true;
            } else {
                $return = false;
            }
        } else {
            /**
             * If no 'new' see if there are 'done' user_plan_steps for given chapter
             */
            $conditions = [
                ['user_plan_days.user_id', $user_id],
                ['user_plan_steps.status', 'done'],
                ['chapters.book_id', $book_no]
            ];
            $done_step = $this->getFirstUserPlanStepAccordingTo($conditions);

            if ($done_step === null) {
                /* if there are no 'new' and 'done' steps than the chapter in unavailable */
                $return = false;
            } else {
                /* if there are no 'new' but here are 'done' chapters than the whole book is available */
                $return = true;
            }
        }

        return $return;
    }

    private function getFirstUserPlanStepAccordingTo(array $conditions)
    {
        /**
         * @TODO make this: getUserPlanStepAccordingTo(array $conditions, bool $get_first = false)
         */
        return DB::table('user_plan_days')
            ->leftJoin('user_plan_steps', 'user_plan_days.id', '=', 'user_plan_steps.user_plan_day_id')
            ->leftJoin('plan_steps', 'user_plan_steps.plan_step_id', '=', 'plan_steps.id')
            ->leftJoin('chapters', 'plan_steps.chapter_id', '=', 'chapters.id')
            ->select('chapters.id', 'chapters.chapter_no', 'chapters.book_id','user_plan_steps.status')
            ->where($conditions)
            ->orderBy('chapters.id', 'asc')->take(1)->first();
    }
}
