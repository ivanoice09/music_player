<script id="artist-view-template" type="text/x-handlebars-template">
    <div class="artist-header d-flex mb-4">
        <div class="artist-image-container me-4">
            <img src="{{artist.image}}" id="artistImage" class="artist-image rounded-circle" width="150" height="150">
        </div>
        <div class="artist-info">
            <h1>{{artist.name}}</h1>
            <p class="text-muted">{{artist.songs.length}} songs</p>
        </div>
    </div>

    <div id="artistSongs">
        {{#if artist.songs.length}}
            <ul class="list-group">
                {{#each artist.songs}}
                    <li class="list-group-item song-item" data-id="{{id}}">
                        <div class="d-flex align-items-center">
                            <img src="{{image}}" width="50" height="50" class="me-3">
                            <div class="flex-grow-1">
                                <h6>{{name}}</h6>
                                <small class="text-muted">{{artist_name}}</small>
                            </div>
                        </div>
                    </li>
                {{/each}}
            </ul>
        {{else}}
            <div class="alert alert-info">No songs found for this artist.</div>
        {{/if}}
    </div>
</script>