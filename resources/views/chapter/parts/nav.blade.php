<form method="post" class="text-center b-chapter-bottom-buttons" onsubmit="return onChapterNavSubmit();"
      name="chapter_nav_buttons" id="chapter_nav">
    {{ csrf_field() }}
    <input type="hidden" name="cid" value="{{$c->id}}">
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#chapter_modal">
            Media
        </button>
        <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#faq"
                aria-expanded="false" onClick="return scrollToBottom();">
            FAQ
        </button>
        <button type="submit" class="btn btn-default" value="next_step" name="chapter_nav_button" form="chapter_nav"
                onclick="document.pressed=this.value">
            Następny Krok >
        </button>
        @if (!$next_book->the_end)
            <button type="submit" class="btn btn-default" value="next_book" name="chapter_nav_button" form="chapter_nav"
                    onclick="document.pressed=this.value">
                Następna Księga >>
            </button>
        @endif
    </div>
</form>