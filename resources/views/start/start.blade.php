@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/start.css') }}" rel="stylesheet">
@endsection
@section('scripts-after')
    <script type="text/javascript" src="{{ asset('js/start.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/verse.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <h2 class="page-header start_header">KSIĘGI</h2>
            @foreach($books as $book_no => $book_data)
                <h4 id="book_{{ $book_no }}_header" class="scroll_to_top start_book_item collapsed"
                    data-toggle="collapse" data-target="#book_{{ $book_no }}" aria-expanded="false">
                    Księga {{ $book_no }} - {{ $book_data['title'] }}
                    @if (isset($book_data['has_new']))
                        <span class="label label-info start_n_label">N</span>
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
            <h2 class="page-header start_header">ULUBIONE WERSY</h2>
            <div class="fav_quotes_block">
                @foreach($fav_verses as $fav)
                    <div id="v_container_{{ $fav->verse_id }}" class="collapse in">
                        <div class="clearfix">
                            <p id="v_{{ $fav->verse_id }}" class="favs_quote verse pull-left"
                               data-toggle="modal" data-target="#verse_{{ $fav->verse_id }}_modal"
                               title="Wers {{ $fav->verse_no }}. {{ $fav->additional_info }}">
                                "{{ $fav->content }}"
                            </p>
                            <p class="favs_quote_origin pull-right">
                                Księga {{ $fav->book_id }},
                                Rozdział {{ $fav->chapter_no }},
                                Wers {{ $fav->verse_no }}
                            </p>
                        </div>
                        <hr>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- FAV VERSE MODAL --}}
    @include('start.verse-modal')

</div>
@endsection
