class SPARouter {
    constructor() {
        this.audioPlayer = document.getElementById('audioPlayer') || this.createAudioPlayer();
        this.baseUrl = URL_ROOT.replace(window.location.origin, '');
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
            if (!link.hasAttribute('data-spa-ignore') && 
                !link.href.includes('#') && 
                !link.classList.contains('no-spa')) {
                e.preventDefault();
                this.navigate(link.href);
            }
        });

        // Handle browser navigation
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.spa) {
                this.loadContent(window.location.pathname);
            }
        });
    }

    

    normalizeUrl(url) {
        // Remove base URL if present
        let normalized = url.replace(window.location.origin, '');
        // Ensure it starts with /
        if (!normalized.startsWith('/')) {
            normalized = '/' + normalized;
        }
        // Remove public if present
        normalized = normalized.replace(/^\/public/, '');
        return normalized;
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
        // Normalize URL
        const normalizedUrl = this.normalizeUrl(url);
        history.pushState({ spa: true }, '', normalizedUrl);
        await this.loadContent(normalizedUrl);
    }
    
    async loadContent(url) {
        try {
            // Show loading indicator
            $('body').addClass('loading');

            // Handle home route specially
            if (url === '/' || url === '/home' || url === '') {
                url = '/main';
            }

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const contentType = response.headers.get('content-type');
            if (contentType.includes('application/json')) {
                return await response.json();
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Get content from #app-content or use full response
            let newContent = $(doc).find('#app-content').html() || html;
            
            // Special handling for home page
            if (url === '/main') {
                const homeContent = $(doc).find('.container').html();
                if (homeContent) {
                    newContent = `<div class="container">${homeContent}</div>`;
                }
            }

            // Preserve player state
            const currentTime = this.audioPlayer.currentTime;
            const isPlaying = !this.audioPlayer.paused;
            const currentSrc = this.audioPlayer.src;

            // Update content
            $('#app-content').html(newContent);
            document.title = doc.title || 'Musyk';

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