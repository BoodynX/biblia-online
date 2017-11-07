@foreach($fav_verses as $fav)
    <div class="modal fade" id="verse_{{ $fav->verse_id }}_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Księga {{ $fav->book_id }} -  Rozdział {{ $fav->chapter_no }}</h4>
                </div>
                <div class="modal-body">
                    <p class="verse_modal_header">Wers {{ $fav->verse_no }}</p>
                    <p>{{ $fav->content }}</p>
                    @if ($fav->additional_info)
                        <p class="verse_modal_header">Dodatkowe informacje</p>
                        <p>{{ $fav->additional_info }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <p id="add_to_fav_error_{{ $fav->verse_id }}" class="collapse text-danger"></p>
                    <button id="fav_button_{{ $fav->verse_id }}" type="button" class="btn btn-default add_to_fav" value="{{ $fav->verse_id }}"
                            data-toggle="collapse" data-target="#v_container_{{  $fav->verse_id  }}">
                            <span id="fav_button_label_{{ $fav->verse_id }}">
                                {{  Config::get('commons.buttons.rem_from_favs')  }}
                            </span>
                        <span id="fav_button_ico_{{ $fav->verse_id }}" aria-hidden="true"
                              class="glyphicon glyphicon-star fav_button_ico fav_on">
                            </span>
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
                </div>
            </div>
        </div>
    </div>
@endforeach