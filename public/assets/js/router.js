class Router {
    constructor() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Intercept all internal links
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href^="/"]');
            if (link && !link.hasAttribute('data-ignore-router')) {
                e.preventDefault();
                this.navigate(link.getAttribute('href'));
            }
        });

        // Handle browser back/forward
        window.addEventListener('popstate', () => {
            this.loadContent(window.location.pathname, false);
        });
    }

    navigate(path, pushState = true) {
        if (pushState) {
            window.history.pushState({}, '', path);
        }
        this.loadContent(path);
    }

    async loadContent(path) {
        try {
            const response = await fetch(path, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const content = await response.text();
            this.updateMainContent(content);
        } catch (error) {
            console.error('Failed to load content:', error);
            // Fallback to full page load
            window.location.href = path;
        }
    }

    updateMainContent(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Update only the main content area (identify a container in your layout)
        const mainContainer = document.querySelector('#main-content');
        const newContent = doc.querySelector('#main-content');
        
        if (mainContainer && newContent) {
            mainContainer.innerHTML = newContent.innerHTML;
        }
        
        // Reinitialize any necessary scripts for the new content
        this.initPageSpecificScripts();
    }
}

// Initialize the router when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.appRouter = new AppRouter();
});