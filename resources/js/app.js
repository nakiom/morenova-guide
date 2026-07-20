import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import Modal from 'bootstrap/js/dist/modal';
import './pathology-progress';
import './ui';

import Alpine from 'alpinejs';

window.bootstrap = { Modal };
window.Alpine = Alpine;

Alpine.start();
