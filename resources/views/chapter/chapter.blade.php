@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/chapter.css') }}" rel="stylesheet">
@endsection
@section('scripts-after')
    <script type="text/javascript" src="{{ asset('js/chapter.js') }}"></script>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div id="chapter_header" class="page-header">
                    <h1>Księga {{$chapter->book_id}} <small>Rozdział {{$chapter->chapter_no}}</small></h1>
                </div>
                <div id="chapter_content">
                    @foreach($chapter->getContent() as $verse)
                        {{$verse}}
                    @endforeach
                </div>
                {{-- NAV AND FAQ --}}
                @include('chapter.faq')
                @include('chapter.nav')
            </div>
        </div>
        {{-- MODAL --}}
        @include('chapter.media')
    </div>
@endsection
