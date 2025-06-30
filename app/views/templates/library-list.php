<script id="library-list-template" type="text/x-handlebars-template">
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
  
  <ul class="list-group">
    {{#each items}}
      <li class="list-group-item library-item {{#if is_pinned}}pinned{{/if}}" 
          data-id="{{id}}"
          data-item-id="{{item_id}}"
          data-type="{{item_type}}">
        <div class="d-flex align-items-center">
          {{#if (eq item_type 'playlist')}}
            <img src="{{image_url}}" width="60" height="60" class="me-3 rounded">
          {{else}}
            <img src="{{image}}" width="60" height="60" class="me-3 rounded">
          {{/if}}
          <div class="flex-grow-1" style="color: white;">
            <h5>{{name}}</h5>
            <p class="mb-0">{{item_type}}</p>
          </div>
          <button class="btn btn-sm btn-outline-secondary pin-btn">
            <i class="fas fa-thumbtack"></i>
          </button>
        </div>
      </li>
    {{/each}}
  </ul>
</script>