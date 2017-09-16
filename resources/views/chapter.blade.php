@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <div class="page-header">
                <h1>Księga {{$c->book_id}} <small>Rozdział {{$c->chapter_no}}</small></h1>
            </div>
            <div>
                @foreach($c->getContent() as $verse)
                    {{$verse}}
                @endforeach
            </div>
            <form action="/ksiega/{{$next_c->book_id}}/rozdzial/{{$next_c->chapter_no}}" method="post" class="text-center b-chapter-bottom-buttons"
                  id="chapter_nav">
                {{ csrf_field() }}
                <div class="btn-group" role="group" aria-label="...">
                    <button type="submit" class="btn btn-default" name="next_step" form="chapter_nav">Następny Krok ></button>
                    <button type="submit" class="btn btn-default" name="next_book" form="chapter_nav">Następna Księga >></button>
                </div>
            </form>
    </div>
</div>
@endsection
