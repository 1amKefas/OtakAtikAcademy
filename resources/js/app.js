import './bootstrap';

// Import Alpine & Plugins
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Setup Alpine
Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();