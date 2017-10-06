<script type="text/javascript">
    function scrollToBottom()
    {
        setTimeout(function (){
            $('html, body').animate({
                scrollTop: $("#bottom").offset().top
            }, 250);
        }, 1);
    }

    function onChapterNavSubmit()
    {
        @if ($next_step->the_end)
        document.chapter_nav_buttons.action ="/koniec";
        @else
            if (document.pressed == 'next_step') {
                document.chapter_nav_buttons.action ="/ksiega/{{$next_step->book_id}}/rozdzial/{{$next_step->chapter_no}}";
            }
            @if (!$next_book->the_end)
        if (document.pressed == 'next_book') {
            document.chapter_nav_buttons.action ="/ksiega/{{$next_book->book_id}}/rozdzial/{{$next_book->chapter_no}}";
        }
            @endif
        @endif
        return true;
    }
</script>