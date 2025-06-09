<script id="album-view-template" type="text/x-handlebars-template">
    <div class="album-header d-flex mb-4">
        <div class="album-image-container me-4">
            <img src="{{album.image}}" id="albumImage" class="album-image" width="150" height="150">
        </div>
        <div class="album-info">
            <h1>{{album.name}}</h1>
            <p class="text-muted">{{album.artist_name}}</p>
            <p class="text-muted">{{album.songs.length}} songs</p>
        </div>
    </div>

    <div id="albumSongs">
        {{#if album.songs.length}}
            <ul class="list-group">
                {{#each album.songs}}
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
            <div class="alert alert-info">No songs found in this album.</div>
        {{/if}}
    </div>
</script>