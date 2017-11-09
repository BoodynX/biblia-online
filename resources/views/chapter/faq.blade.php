<div class="collapse" id="faq">
    <div id="faq_header" class="page-header">
        <h1>FAQ <small>Często zadawane pytania</small></h1>
    </div>
    <div id="faq_content">
        @foreach($chapter->chapterFaqs as $q)
            <p class="ch_question">
                <span data-toggle="collapse" data-target="#faq{{ $q->id }}" aria-expanded="false">{{ $q->question }}</span>
            </p>
            <p class="collapse_hack_2">&nbsp;</p>
            <p id="faq{{ $q->id }}" class="collapse ch_answer">{{ $q->answere }}</p>
        @endforeach
            <p class="ch_question">
                <span id="faq_form_question" class="scroll_target_to_top" data-toggle="collapse" data-target="#faq_form" aria-expanded="false">
                    Masz więcej pytań? Napisz do nas!
                </span>
            </p>
            <p class="collapse_hack_2">&nbsp;</p>
            <form id="faq_form" class="collapse">
                {{ csrf_field() }}
                <input type="hidden" name="cid" value="{{ $chapter->id }}">
                <textarea id="user_question" class="form-control" rows="5" name="user_question"></textarea>
                <button id="faq_submit" type="submit" class="btn btn-default" form="faq_form" value="Submit">
                    Wyślij
                </button>
            </form>
            <p id="ajaxResponse" class="collapse ch_answer"></p>
    </div>
    <p class="collapse_hack_1">&nbsp;</p>
</div>