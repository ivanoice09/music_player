<script id="library-grid-template" type="text/x-handlebars-template">
  <div class="library-header d-flex justify-content-between align-items-center mb-4">
    <h1>Library</h1>
    <div class="library-controls">
      <div class="btn-group">
        <button class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
          <i class="fas fa-th"></i> Layout
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item layout-option" data-layout="grid"><i class="fas fa-th"></i> Grid</a>
          <a class="dropdown-item layout-option" data-layout="list"><i class="fas fa-list"></i> List</a>
        </div>
      </div>
      <div class="btn-group ms-2">
        <button class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
          <i class="fas fa-sort"></i> Sort
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item sort-option" data-sort="recent"><i class="fas fa-clock"></i> Recently Added</a>
          <a class="dropdown-item sort-option" data-sort="artist"><i class="fas fa-user"></i> By Artist</a>
          <a class="dropdown-item sort-option" data-sort="alpha"><i class="fas fa-sort-alpha-down"></i> Alphabetical</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{#each items}}
      <div class="col-md-3 col-sm-6 mb-4">
        <div class="card h-100 library-item {{#if is_pinned}}pinned{{/if}}" 
             data-id="{{id}}"
             data-item-id="{{item_id}}"
             data-type="{{item_type}}">
          {{#if (eq item_type 'playlist')}}
            <!-- Changed from image_url to image to much the metadata stucture: -->
            <img src="{{image}}" class="card-img-top" alt="{{name}}">
          {{else}}
            <img src="{{image}}" class="card-img-top" alt="{{name}}">
          {{/if}}
          <div class="card-body" style="color: white;">
            <h5 class="card-title">{{name}}</h5>
            <p class="card-text">{{item_type}}</p>
            <button class="btn btn-sm btn-outline-secondary pin-btn">
              <i class="fas fa-thumbtack"></i>
            </button>
          </div>
        </div>
      </div>
    {{/each}}
  </div>
</script>