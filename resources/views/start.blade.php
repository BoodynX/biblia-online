@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @foreach($books as $book_no => $book_title)
                <div class="panel panel-default">
                    <div class="panel-heading">Księga {{$book_no}} - {{$book_title}}</div>
                    <div class="panel-body">
                        @foreach($chapters[$book_no] as $c)
                            <p>
                                @if ($c->getStatus() == 'new')
                                    @if ($c->getIsNext())
                                        <a href="ksiega/{{$c->book_id}}/rozdzial/{{$c->chapter_no}}">
                                            Rozdział {{$c->chapter_no}}
                                        </a>
                                        <span class="label label-info">Nowy</span>
                                    @else
                                        Rozdział {{$c->chapter_no}}
                                    @endif
                                @else
                                    <a href="ksiega/{{$c->book_id}}/rozdzial/{{$c->chapter_no}}">Rozdział {{$c->chapter_no}}</a>
                                @endif
                            </p>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
