<script id="playlist-view-template" type="text/x-handlebars-template">
  <div class="playlist-header d-flex mb-4">
    <div class="playlist-image-container me-4">
      <img src="{{playlist.image_url}}" id="playlistImage" class="playlist-image">
      <input type="file" id="playlistImageUpload" accept="image/*" style="display: none;">
      <button class="btn btn-sm btn-outline-light change-image-btn">
        <i class="fas fa-camera"></i>
      </button>
    </div>
    <div class="playlist-info">
      <h1 contenteditable="true" id="playlistName">{{playlist.name}}</h1>
      <!-- count how many songs in a playlist -->
      <p>{{playlist.songs.length}} songs</p>
    </div>
  </div>

  <div class="playlist-search mb-4">
    <div class="input-group">
      <input type="text" class="form-control" placeholder="Add songs to playlist...">
      <button class="btn btn-outline-light"><i class="fas fa-search"></i></button>
    </div>
  </div>

  <div id="playlistSongs">
    {{#if playlist.songs.length}}
      <ul class="list-group">
        {{#each playlist.songs}}
          <li class="list-group-item song-card text-white" 
              data-id="{{id}}"
              data-audio="{{audio}}" 
              data-title="{{name}}" 
              data-artist="{{artist_name}}"
              data-image="{{image}}">
            <div class="d-flex align-items-center">
              <img src="{{image}}" width="50" height="50" class="me-3">
              <div class="flex-grow-1">
                <h6>{{name}}</h6>
                <small class="text-white">{{artist_name}}</small>
              </div>
              <button class="btn btn-sm btn-outline-danger remove-song-btn">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </li>
        {{/each}}
      </ul>
    {{else}}
      <div class="alert alert-info">This playlist is empty. Search for songs to add.</div>
    {{/if}}
  </div>
</script>