<?php

namespace App\Http\Controllers;

use App\Models\UserPlanStep;
use Illuminate\Http\Request;
use App\Models\Chapter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ChapterUserQuestion;
use stdClass;

/**
 * Class ChaptersController
 * @package App\Http\Controllers
 */
class ChaptersController extends Controller
{
    /** @const number of books in the bible. */
    const LAST_BOOK = 73;

    /**
     * Gathers necessary data required to display the chapter and calls the view.
     *
     * @param  int $book_no
     * @param  int $chapter_no
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
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

    /**
     * Shows the next step/chapter for the user to read after clicking "Next Step" in the Chapter navigation.
     * @see routes/web.php - Route::post('/ksiega/{book}/rozdzial/{chapter}', 'ChaptersController@showNext');
     *
     * @param  int $book_no
     * @param  int $chapter_no
     * @param  Request $r
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showNext(int $book_no, int $chapter_no, Request $r)
    {
        /* Mark current user step as read. */
        $this->currentUserStepRead($r);
        /* Go to next chapter. */
        return $this->show($book_no, $chapter_no);
    }

    /**
     * When reader reaches the last available chapter, we need to give him the ability to mark it as red.
     * This method is called by a button "The End" in the chapter navigation,
     * when there are no more new steps/chapters to read.
     *
     * @see Route::post('/last', 'ChaptersController@showEnd');
     *
     * @param  Request $r
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function showEnd(Request $r)
    {
        /* Mark current user step as read. */
        $this->currentUserStepRead($r);
        /* Go to end screen. */
        return redirect('start');
    }

    /**
     * Redirects users to the first unread step/chapter in the plan.
     * It is called via '/nastepny_krok' address/route
     * @see routes/web.php - Route::get('/nastepny_krok', 'ChaptersController@findAndShowNextStep');
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function findAndShowNextStep()
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

    /**
     * Stores users question passed via the FAQ question form. The question is sent via AJAX.
     * @see Route::post('/chapter/send_question', 'ChaptersController@storeQuestion');
     *
     * @param  Request              $request
     * @param  ChapterUserQuestion  $question
     * @return string
     */
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
     * Get the next step after given chapter or a given book.
     * If you want to get the first step in the next book submit $chapter_id = false and $book_no of current book.
     * If you want to get users next step in general submit $chapter_id = false and do not submit $book_no.
     *
     * @param  int|bool  $chapter_id     Next after this chapter id.
     * @param  int|bool  $book_no        Next after this book id/no.
     * @return stdClass
     */
    private function nextStep($chapter_id, $book_no = false) : stdClass
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
            $next_step = $this->getUserPlanStepsAccordingTo($conditions, true);

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
                $next_step = $this->getUserPlanStepsAccordingTo($conditions, true);
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
     * @param Request $r
     */
    private function currentUserStepRead(Request $r)
    {
        $user_id = Auth::id();
        $prev_chapter_id = intval($r->input('cid'));

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
    private function isChapterUnlocked(int $book_no, int $chapter_no)
    {
        $user_id = Auth::id();
        /* Assume chapter is locked. */
        $access = false;
        /* Get list of all chapters from the requested book, available for the user to read or unlock today. */
        $conditions = [
            ['user_plan_days.user_id', $user_id],
            ['chapters.book_id', $book_no]
        ];
        $user_plan_steps = $this->getUserPlanStepsAccordingTo($conditions);
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
    private function getUserPlanStepsAccordingTo(array $conditions, bool $get_first = false)
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
}
