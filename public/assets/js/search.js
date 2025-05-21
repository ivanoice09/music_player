document.addEventListener('DOMContentLoaded', function () {
    // Search suggestions
    const searchInput = document.getElementById('searchInput');
    const suggestionsContainer = document.getElementById('searchSuggestions');

    if (searchInput && suggestionsContainer) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim();

            if (query.length > 2) {
                fetch('<?php echo URL_ROOT; ?>/music/getSuggestions?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(suggestions => {
                        suggestionsContainer.innerHTML = '';

                        if (suggestions.length > 0) {
                            suggestions.forEach(suggestion => {
                                const suggestionElement = document.createElement('a');
                                suggestionElement.href = '#';
                                suggestionElement.className = 'list-group-item list-group-item-action';
                                suggestionElement.textContent = suggestion.name + ' - ' + suggestion.artist;
                                suggestionElement.addEventListener('click', function (e) {
                                    e.preventDefault();
                                    searchInput.value = suggestion.name;
                                    suggestionsContainer.innerHTML = '';
                                    document.getElementById('searchForm').submit();
                                });
                                suggestionsContainer.appendChild(suggestionElement);
                            });
                            suggestionsContainer.style.display = 'block';
                        } else {
                            suggestionsContainer.style.display = 'none';
                        }
                    });
            } else {
                suggestionsContainer.style.display = 'none';
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });
    }

    // Pagination
    const pageLinks = document.querySelectorAll('.page-link[data-page]');
    const pageInput = document.getElementById('pageInput');

    pageLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            pageInput.value = this.getAttribute('data-page');
            document.getElementById('searchForm').submit();
        });
    });
});