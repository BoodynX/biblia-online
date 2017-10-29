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

    public function show(int $book_no, int $chapter_no)
    {
        /* Check if user unlocked this chapter */
        if (! $this->isChapterUnlocked($book_no, $chapter_no)) {
            return redirect()->route('start');
        }
        /* Get required data and display the chapter */
        $chapter   = Chapter::byBookChapter($book_no, $chapter_no);
        $next_step = $this->nextStep($chapter->id);
        $next_book = $this->nextStepInNextBook($chapter->book_id);
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

    /**
     * @param int       $chapter_id     Next after this chapter id
     * @param int|bool  $book_no           Next after this book id/no
     * @return stdClass
     */
    private function nextStep($chapter_id, $book_no = false) : stdClass
    {
        $left_to_read = UserPlanStep::where('status', 'new')->count();
        if ($left_to_read < 2) {
            /**
             * If there are no unread chapters or only one is left, hide the "next step" and "next chapter" navigation
             * buttons and show a "Start" button
             */
            $next_step = new stdClass();
            $next_step->loop = false;
            $next_step->the_end = true;
            return $next_step;
            /**
             * THE END
             */
        }
        $user_id = Auth::id();
        $conditions = [
            ['user_plan_days.user_id', $user_id],
            ['user_plan_steps.status', 'new']
        ];
        if ($chapter_id) {
            $conditions[] = ['chapters.id', '>', $chapter_id];
        }
        if ($book_no !== false) {
            $conditions[] = ['chapters.book_id', '>', $book_no];
        }
        $next_step = $this->checkUserPlanDays($conditions);
        /* If there is no unread chapters after the current one, than check if there are any unread chapters at all */
        if ($next_step === null) {
            $conditions = [
                ['user_plan_days.user_id', $user_id],
                ['user_plan_steps.status', 'new']
            ];
            $next_step = $this->checkUserPlanDays($conditions);
            $next_step_proto = new stdClass();
            $next_step_proto->loop = true;
            $next_step_proto->chapter_no = $next_step->chapter_no;
            $next_step_proto->book_id = $next_step->book_id;
            $next_step = $next_step_proto;
        } else {
            $next_step->loop = false;
        }

        /* If its not the end let them know :) */
        $next_step->the_end = false;

        return $next_step;
    }

    private function nextStepInNextBook($book_no) : stdClass
    {
        if ($book_no < self::LAST_BOOK) {
            /* If there are no more books unlocked the nextStep will return stdObject->the_end = true */
            $next_book = $this->nextStep(false, $book_no);
        } else {
            $next_book = new stdClass;
            $next_book->the_end = true;
        }
        return $next_book;
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
        $user_id = Auth::id();

        /* @TODO access control logic */
        $conditions = [
            ['user_plan_days.user_id', $user_id],
            ['user_plan_steps.status', 'new'],
            ['chapters.book_id', $book_no]
        ];

        $new_chapter = $this->checkUserPlanDays($conditions);

        /* When every thing is read than there is no 'user_plan_steps.status' = 'new' */
        if ($new_chapter === null) {
            return true;
        }

        if (!isset($new_chapter->chapter_no)) {
            return false;
        }

        if ($new_chapter->chapter_no >= $chapter_no) {
            return true;
        } else {
            return false;
        }
    }

    private function checkUserPlanDays(array $conditions)
    {
        return DB::table('user_plan_days')
            ->leftJoin('user_plan_steps', 'user_plan_days.id', '=', 'user_plan_steps.user_plan_day_id')
            ->leftJoin('plan_steps', 'user_plan_steps.plan_step_id', '=', 'plan_steps.id')
            ->leftJoin('chapters', 'plan_steps.chapter_id', '=', 'chapters.id')
            ->select('chapters.id', 'chapters.chapter_no', 'chapters.book_id','user_plan_steps.status')
            ->where($conditions)
            ->orderBy('chapters.id', 'asc')->take(1)->first();
    }
}
