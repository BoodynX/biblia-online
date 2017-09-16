@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"> Rozdział {{$c->chapter_no}}</div>

                <div class="panel-body">
                    @foreach($c->getContent() as $verse)
                        {{$verse}}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
