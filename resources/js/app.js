import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';


window.Pusher = Pusher;

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// console.log('Vite Environment:', import.meta.env);

// window.Echo = new Echo({
//     broadcaster: 'reverb',
//     key: 'iq2rtanjhcu8dn6ahnih',
//     wsHost: '127.0.0.1',
//     wsPort: 8080,
//     wssPort: 8080,
//     forceTLS: false, 
//     enabledTransports: ['ws', 'wss'],
// });

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME === 'https'),
    enabledTransports: ['ws', 'wss'],
});