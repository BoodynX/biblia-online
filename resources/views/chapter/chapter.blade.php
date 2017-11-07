@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/chapter.css') }}" rel="stylesheet">
@endsection
@section('scripts-after')
    <script type="text/javascript" src="{{ asset('js/chapter.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/verse.js') }}"></script>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                <div id="chapter_header" class="page-header">
                    <h1>Księga {{ $chapter->book_id }} <small>Rozdział {{ $chapter->chapter_no }}</small></h1>
                </div>
                <div id="chapter_content">
                    @foreach($chapter->verses as $vid => $verse)
                        <span id="v_{{ $vid }}" class="verse" data-toggle="modal" data-target="#verse_{{ $vid }}_modal"
                              title="Wers {{ $verse->verse_no }}. {{ $verse->additional_info }}">
                            {{ $verse->content }}
                        </span>
                    @endforeach
                </div>
                {{-- NAV AND FAQ --}}
                @include('chapter.faq')
                @include('chapter.nav')
            </div>
        </div>
        {{-- MODALS --}}
        @include('chapter.media-modal')
        @include('chapter.verse-modal')
    </div>
@endsection
