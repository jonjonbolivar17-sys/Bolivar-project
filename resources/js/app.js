

import Alpine from 'alpinejs';
import weatherWidget from './weather-widget';

window.Alpine = Alpine;

Alpine.data('weatherWidget', weatherWidget);

Alpine.start();
