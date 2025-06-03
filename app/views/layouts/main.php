<!-- MAIN CONTENT VIEW -->
<div class="container">
    <div class="row mt-3" id="mainView">
        <?php if (!$data['search_performed']): ?>
            <div class="col-12 text-center text-muted">
                Type in the search bar to find music    
            </div>
        <?php endif; ?>
    </div>
</div>

<script id="song-template" type="text/x-handlebars-template">
    {{#if title}}
        <h2 class="mb-4">{{title}}</h2>
    {{/if}}
    <div class="row">
        {{#each songs}}
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="song-card card h-100"
                    data-audio="{{audio}}" 
                    data-title="{{name}}" 
                    data-artist="{{artist_name}}" 
                    data-artwork="{{image}}">
                    <img src="{{image}}" class="card-img-top" alt="{{name}}">
                    <div class="card-body">
                        <h5 class="card-title">{{name}}</h5>
                        <p class="card-text text-muted">{{artist_name}}</p>
                    </div>
                </div>
            </div>
        {{/each}}
    </div>
</script>