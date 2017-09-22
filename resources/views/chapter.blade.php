@extends('layouts.app')

@section('content')
    <script type="text/javascript">
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
                <form method="post" class="text-center b-chapter-bottom-buttons" onsubmit="return onChapterNavSubmit();"
                      name="chapter_nav_buttons" id="chapter_nav">
                    {{ csrf_field() }}
                    <input type="hidden" name="cid" value="{{$c->id}}">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="submit" class="btn btn-default" value="next_step" name="chapter_nav_button" form="chapter_nav"
                                onclick="document.pressed=this.value">Następny Krok ></button>
                        @if ($c->book_id !== 3)
                            <button type="submit" class="btn btn-default" value="next_book" name="chapter_nav_button" form="chapter_nav"
                                    onclick="document.pressed=this.value">Następna Księga >></button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
