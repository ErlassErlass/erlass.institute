// Import Bootstrap Bundle (includes Popper)
import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
window.bootstrap = bootstrap;

// jQuery setup
import jQuery from 'jquery';
window.$ = jQuery;
window.jQuery = jQuery;
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
