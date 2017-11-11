<div id="multimedia" class="collapse">
    <div id="multimedia_header" class="page-header">
        <h1>MULTIMEDIA <small>Dodatkowe informacje</small></h1>
    </div>
    <div id="multimedia_content">
        @foreach ($multimedia as $media)
            @if($media->type == 'youtube')
                <p>Youtube video found!</p>
                <p>{{ $media->comment }}</p>
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src={{ $media->address }} allowfullscreen></iframe>
                </div>
            @elseif($media->type == 'image')
                <p>Image found!</p>
                <p>{{ $media->comment }}</p>
                <img src="{{ '/images/'.$media->address }}" class="img-responsive center-block">
            @elseif($media->type == 'link')
                <p>Text found</p>
                <p><a href="{{ $media->address }}">{{ $media->comment }}</a></p>
            @endif
            <hr>
        @endforeach
    </div>
    <p class="collapse_hack_1">&nbsp;</p>
</div>