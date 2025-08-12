import { mainContainer, templateCache } from '../config/config.js';

export async function loadTemplate(templateName) {
    if (!templateCache[templateName]) {
        try {
            const url = `templates?name=${templateName}`;
            const response = await fetch(url);

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const templateElement = doc.getElementById(`${templateName}-template`);
            if (!templateElement) {
                throw new Error(`Template ${templateName}-template not found in response`);
            }

            templateCache[templateName] = templateElement.innerHTML;

            // Add to DOM only if not already present
            if (!document.getElementById(`${templateName}-template`)) {
                document.body.insertAdjacentHTML('beforeend', html);
            }

        } catch (error) {
            console.error(`Failed to load template ${templateName}:`, error);
            throw error;
        }
    }
    return templateCache[templateName];
}

export async function setViewTemplate(results, title = '', templateName = 'song-grid') {
    if (results?.length > 0) {
        await loadTemplate(templateName);
        const source = templateCache[templateName];
        const template = Handlebars.compile(source);
        mainContainer.html(template({ title, songs: results }));
    } else {
        mainContainer.html('<div class="alert alert-warning">No results found.</div>');
    }
}