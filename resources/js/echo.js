import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],

    // ── Reconexión controlada ──────────────────
    activityTimeout: 120000,   // 2 min sin actividad antes de ping
    pongTimeout: 10000,        // espera 10s la respuesta del ping
    unavailableTimeout: 10000, // 10s para marcar como no disponible
});

// ── Pausar cuando la pestaña está oculta ──────
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        window.Echo.connector.pusher.connection.disconnect();
    } else {
        window.Echo.connector.pusher.connection.connect();
    }
});