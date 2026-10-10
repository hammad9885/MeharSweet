import './bootstrap';

import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import Example from './components/Example';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Inertia React App Mount
const appElement = document.getElementById('app');
if (appElement && appElement.dataset.page) {
    createInertiaApp({
        title: (title) => `${title} - ${appName}`,
        resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
        setup({ el, App, props }) {
            const root = createRoot(el);
            root.render(<App {...props} />);
        },
        progress: {
            color: '#4B5563',
        },
    });
}

// Standalone React mount for Blade views
const reactRoot = document.getElementById('react-root');
if (reactRoot) {
    createRoot(reactRoot).render(<Example />);
}
