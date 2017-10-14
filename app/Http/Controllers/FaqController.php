<?php

namespace App\Http\Controllers;

use App\Models\ChapterUserQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    public function store(Request $request)
    {
        $question = new ChapterUserQuestion;
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
