<script id="song-list-template" type="text/x-handlebars-template">
  {{#if title}}<h2 class="mb-4">{{title}}</h2>{{/if}}
  <ul class="list-group">
    {{#each songs}}
      <li class="list-group-item song-card" 
          data-audio="{{audio}}" 
          data-title="{{name}}" 
          data-artist="{{artist_name}}"
          data-image="{{image}}">
        <div class="d-flex">
          <img src="{{image}}" width="60" height="60" class="me-3">
          <div>
            <h5>{{name}}</h5>
            <p class="mb-0">{{artist_name}}</p>
          </div>
        </div>
      </li>
    {{/each}}
  </ul>
</script>