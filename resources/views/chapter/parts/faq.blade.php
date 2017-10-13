<div class="collapse" id="faq">
    <div id="faq_header" class="page-header">
        <h1>FAQ <small>Często zadawane pytania</small></h1>
    </div>
    <div id="faq_content">
        @foreach($chapter->chaptersFaqs as $q)
            <p class="ch_question">{{$q->question}}</p>
            <p class="ch_answer">{{$q->answere}}</p>
        @endforeach
    </div>
</div>