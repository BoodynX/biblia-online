@extends('layouts.app')

@section('content')
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


    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <div id="chapter_header" class="page-header">
                    <h1>Księga {{$c->book_id}} <small>Rozdział {{$c->chapter_no}}</small></h1>
                </div>
                <div id="chapter_content">
                    @foreach($c->getContent() as $verse)
                        {{$verse}}
                    @endforeach
                </div>
                <div class="collapse" id="faq">
                    <div id="faq_header" class="page-header">
                        <h1>FAQ <small>Często zadawane pytania</small></h1>
                    </div>
                    <div id="faq_content">
                        Masz pytania? Pisz :D
                    </div>
                </div>
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
            </div>
        </div>
        <div class="modal fade" id="chapter_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Media... multimedia</h4>
                    </div>
                    <div class="modal-body">
                        Tutaj kiedyś będą media :D
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="bottom"></div>
    </div>
@endsection
