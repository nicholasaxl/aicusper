import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Realtime is optional. When VITE_REVERB_APP_KEY is missing at build time (the Docker build has no .env),
// Pusher throws on construction; that used to abort this whole script before Alpine started,
// which killed every dropdown and the mobile menu. Never let it break the page.
try {
    if (import.meta.env.VITE_REVERB_APP_KEY) {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
            wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
            forceTLS: false,
            enabledTransports: ['ws'],
        });

        window.Echo.channel('capture-channel')
            .listen('.capture-triggered', (e) => {
                console.log("📡 trigger in bootstrap", e);
                window.dispatchEvent(new Event('capture-trigger'));
            });
    }
} catch (error) {
    console.warn('Realtime (Echo) disabled:', error);
}

