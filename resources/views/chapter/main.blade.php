@extends('layouts.app')

@section('content')
    @include('chapter.js.scripts')
    @include('chapter.css.style')

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

                {{-- NAV AND FAQ --}}
                @include('chapter.parts.faq')
                @include('chapter.parts.nav')
            </div>
        </div>

        {{-- MODAL --}}
        @include('chapter.parts.media')

        <div id="bottom"></div>
    </div>
@endsection
