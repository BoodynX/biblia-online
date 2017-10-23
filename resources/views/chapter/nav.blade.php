<form method="post" class="text-center b-chapter-bottom-buttons" name="chapter_nav_buttons" id="chapter_nav">
    {{ csrf_field() }}
    <input type="hidden" name="cid" value="{{$chapter->id}}">
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#chapter_media_modal">
            Media
        </button>
        <button type="button" class="btn btn-default scrollTo" data-toggle="collapse" data-target="#faq"
                aria-expanded="false">
            FAQ
        </button>
        <button type="submit" class="btn btn-default navButton" value="next_step" name="chapter_nav_button" form="chapter_nav"
                data-book="{{$next_step->book_id}}" data-chapter="{{$next_step->chapter_no}}">
            Następny Krok >
        </button>
        @if (!$next_book->the_end)
            <button type="submit" class="btn btn-default navButton" value="next_book" name="chapter_nav_button" form="chapter_nav"
                    data-book="{{$next_book->book_id}}" data-chapter="{{$next_book->chapter_no}}">
                Następna Księga >>
            </button>
        @endif
    </div>
</form>