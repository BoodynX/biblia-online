<?php

namespace App\Http\Controllers;

use App\Models\Multimedia;
use Auth;
use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\ChapterUserQuestion;
use App\CC\ReaderProgress;

/**
 * Class ChaptersController
 * @package App\Http\Controllers
 */
class ChaptersController extends Controller
{
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
        if (! ReaderProgress::isChapterUnlocked($book_no, $chapter_no)) {
            return redirect()->route('start');
        }
        /* Get chapter data */
        $chapter   = Chapter::byBookChapter($book_no, $chapter_no);
        /* Get next step in general */
        $next_step = ReaderProgress::nextStep($chapter->id);
        /* Get next step in next book */
        $next_book = ReaderProgress::nextStep(false, $chapter->book_id);
        /* Get multimedia */
        $multimedia = Multimedia::where('chapter_id', $chapter->id)->get();

        return view('chapter.chapter', compact('chapter', 'next_step', 'next_book', 'multimedia'));
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
        ReaderProgress::markStepAsRead($r->input('cid'));
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
        ReaderProgress::markStepAsRead($r->input('cid'));
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
        $next_chapter = ReaderProgress::nextStep(false);
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
}
