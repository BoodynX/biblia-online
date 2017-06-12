@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{$chapter->getTitle()}}</div>

                <div class="panel-body">
                    @foreach($chapter->getVerses() as $verse)
                        {{$verse}}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
