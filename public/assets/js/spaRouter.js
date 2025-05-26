class SPARouter {
    constructor() {
        this.audioPlayer = document.getElementById('audioPlayer') || this.createAudioPlayer();
        this.init();
    }

    createAudioPlayer() {
        const audio = document.createElement('audio');
        audio.id = 'audioPlayer';
        audio.style.display = 'none';
        document.body.appendChild(audio);
        return audio;
    }

    init() {
        this.setupEventListeners();
        this.patchExistingFunctions();
    }

    setupEventListeners() {
        // Handle internal navigation
        $(document).on('click', 'a[href^="/"], a[href^="' + window.location.origin + '"]', (e) => {
            const link = e.currentTarget;
            if (!link.hasAttribute('data-spa-ignore')) {
                e.preventDefault();
                this.navigate(link.href);
            }
        });

        // Handle browser navigation
        window.addEventListener('popstate', () => {
            this.loadContent(window.location.pathname);
        });
    }

    patchExistingFunctions() {
        // Patch the existing loadSearchPage function
        if (typeof loadSearchPage === 'function') {
            const originalLoadSearchPage = loadSearchPage;
            loadSearchPage = (query) => {
                const searchUrl = `${URL_ROOT}/search?q=${encodeURIComponent(query)}`;
                this.navigate(searchUrl);
            };
        }
    }

    async navigate(url) {
        history.pushState({}, '', url);
        await this.loadContent(url);
    }

    async loadContent(url) {
        try {
            // Show loading indicator if needed
            $('body').addClass('loading');

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Handle different response types
            if (url.includes('searchResults')) {
                // This is an API response, handle it differently
                return JSON.parse(html);
            } else {
                // This is HTML content
                const newContent = $(doc).find('#app-content').html() || html;

                // Preserve player state
                const currentTime = this.audioPlayer.currentTime;
                const isPlaying = !this.audioPlayer.paused;
                const currentSrc = this.audioPlayer.src;

                // Update content
                $('#app-content').html(newContent);
                document.title = doc.title;

                // Restore player state
                if (currentSrc) {
                    this.audioPlayer.src = currentSrc;
                    this.audioPlayer.currentTime = currentTime;
                    if (isPlaying) {
                        this.audioPlayer.play().catch(e => console.log('Autoplay prevented:', e));
                    }
                }

                // Reinitialize components
                this.reinitializeComponents();
            }

        } catch (error) {
            console.error('Failed to load content:', error);
            window.location.href = url;
        } finally {
            $('body').removeClass('loading');
        }
    }

    reinitializeComponents() {
        // Reinitialize autosearch if it exists
        if (typeof performSearch === 'function' && window.location.pathname.includes('/search')) {
            const urlParams = new URLSearchParams(window.location.search);
            const query = urlParams.get('q');
            if (query) performSearch(query);
        }

        // Reinitialize player if it exists
        if (window.playerState) {
            playerState.init();
        }

        // Trigger custom event
        $(document).trigger('spa:loaded');
    }
}

// Initialize when DOM is ready
$(document).ready(() => {
    window.appRouter = new SPARouter();
});