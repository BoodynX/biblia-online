@foreach($chapter->verses as $vid => $verse)
    <div class="modal fade" id="verse_{{$vid}}_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Księga {{$chapter->book_id}} -  Rozdział {{$chapter->chapter_no}}</h4>
                </div>
                <div class="modal-body">
                    <p class="verse_modal_header">Wers {{$verse->verse_no}}</p>
                    <p>{{$verse->content}}</p>
                    @if ($verse->additional_info)
                        <p class="verse_modal_header">Dodatkowe informacje</p>
                        <p>{{$verse->additional_info}}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <p id="add_to_fav_error_{{$verse->id}}" class="collapse text-danger"></p>
                    <button id="fav_button_{{$verse->id}}" type="button" class="btn btn-default add_to_fav" value="{{$verse->id}}">
                            <span id="fav_button_label_{{$verse->id}}">
                                {{-- Change the text of the fav button --}}
                                @if($verse->verseUserFavs()->first() !== null)
                                    {{ Config::get('commons.buttons.rem_from_favs') }}
                                @else
                                    {{ Config::get('commons.buttons.add_to_favs') }}
                                @endif
                            </span>
                        <span id="fav_button_ico_{{$verse->id}}" aria-hidden="true"
                              {{-- Change the color of the star/fav icon --}}
                              @if($verse->verseUserFavs()->first() !== null)
                              class="glyphicon glyphicon-star fav_button_ico fav_on"
                              @else
                              class="glyphicon glyphicon-star fav_button_ico fav_off"
                                @endif
                        ></span>
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
                </div>
            </div>
        </div>
    </div>
@endforeach