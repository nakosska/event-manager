// Bootstrap JS
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Laravel axios setup
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
