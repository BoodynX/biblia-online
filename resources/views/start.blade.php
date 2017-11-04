@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/start.css') }}" rel="stylesheet">
@endsection
@section('scripts-after')
    <script type="text/javascript" src="{{ asset('js/start.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <h2 class="page-header">KSIĘGI</h2>
            @foreach($books as $book_no => $book_data)
                <h4 id="book_{{ $book_no }}_header" class="scroll_to_top collapsed"
                    data-toggle="collapse" data-target="#book_{{ $book_no }}" aria-expanded="false">
                    Księga {{ $book_no }} - {{ $book_data['title'] }}
                    @if (isset($book_data['has_new']))
                        <span class="label label-info">N</span>
                    @endif
                </h4>
                <div id="book_{{ $book_no }}" class="collapse">
                <ul class="list-unstyled chapters_list">
                    @foreach($chapters[$book_no] as $c)
                        <li>
                            @if ($c->getStatus() == 'new')
                                @if ($c->getIsNext())
                                    <a href="ksiega/{{ $c->book_id }}/rozdzial/{{ $c->chapter_no }}" class="chapter_list_item">
                                        Rozdział {{ $c->chapter_no }}
                                        <span class="label label-info">Nowy</span>
                                    </a>
                                @else
                                    <span class="chapter_list_item">Rozdział {{ $c->chapter_no }}</span>
                                @endif
                            @else
                                <a href="ksiega/{{ $c->book_id }}/rozdzial/{{ $c->chapter_no }}" class="chapter_list_item">
                                    Rozdział {{ $c->chapter_no }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
                </div>
            @endforeach
        </div>
        <div class="col-md-6 col-sm-12">
            <h2 class="page-header">ULUBIONE WERSY</h2>
            <div class="fav_quotes_block">
                @foreach($fav_verses as $fav)
                    <div class="clearfix">
                        <p class="favs_quote pull-left">"{{ $fav->content }}"</p>
                        <p class="favs_quote_origin pull-right">
                            Księga {{ $fav->book_id }},
                            Rozdział {{ $fav->chapter_no }},
                            Wers {{ $fav->verse_no }}
                        </p>
                    </div>
                    <hr>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
