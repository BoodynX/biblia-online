@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Księga I</div>
                <div class="panel-body">
                    @foreach($chapters[1] as $c)
                        <p>
                            <a href="ksiega/{{$c->book_id}}/rozdzial/{{$c->chapter_no}}">Rozdział {{$c->chapter_no}} - {{$c->title}}</a>
                            @if ($c->getStatus() == 'new') <span class="label label-info">Nowy</span> @endif
                        </p>
                    @endforeach
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Księga II</div>
                <div class="panel-body">
                    @foreach($chapters[2] as $c)
                        <p>
                            <a href="ksiega/{{$c->book_id}}/rozdzial/{{$c->chapter_no}}">Rozdział {{$c->chapter_no}} - {{$c->title}}</a>
                            @if ($c->getStatus() == 'new') <span class="label label-info">Nowy</span> @endif
                        </p>
                    @endforeach
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Księga III</div>
                <div class="panel-body">
                    @foreach($chapters[3] as $c)
                        <p>
                            <a href="ksiega/{{$c->book_id}}/rozdzial/{{$c->chapter_no}}">Rozdział {{$c->chapter_no}} - {{$c->title}}</a>
                            @if ($c->getStatus() == 'new') <span class="label label-info">Nowy</span> @endif
                        </p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
