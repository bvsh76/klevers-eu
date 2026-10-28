import Alpine from 'alpinejs';
import consent from './consent';

window.Alpine = Alpine;
Alpine.data('consent', consent);
Alpine.start();