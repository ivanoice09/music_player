<script id="song-grid-template" type="text/x-handlebars-template">
  <!-- TEMPLATE TYPE: GRID VIEW -->
  {{#if title}}<h2 class="mb-4">{{title}}</h2>{{/if}}
  <div class="row">
    {{#each songs}}
      <div class="col-md-3 col-sm-6 mb-4">
        <div class="card h-100 song-card text-white"
             data-id="{{id}}"               
             data-audio="{{audio}}" 
             data-title="{{name}}" 
             data-artist="{{artist_name}}"
             data-image="{{image}}">
          <img src="{{image}}" class="card-img-top" alt="{{name}}">
          <div class="card-body">
            <h5 class="card-title">{{name}}</h5>
            <p class="card-text">{{artist_name}}</p>
          </div>
        </div>
      </div>
    {{/each}}
  </div>
</script>