// Equality helper
Handlebars.registerHelper('eq', function (a, b, options) {
    // When used as inline helper (no options parameter)
    if (arguments.length === 2 || !options.fn) {
        return a === b;
    }
    // When used as block helper
    return a === b ? options.fn(this) : options.inverse(this);
});

// JSON stringify helper
Handlebars.registerHelper('json', function (context) {
    return JSON.stringify(context);
});

// Format duration (seconds to MM:SS)
Handlebars.registerHelper('formatDuration', function (seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
});