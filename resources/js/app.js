import './bootstrap';
import '../css/app.css';
import Alpine from 'alpinejs';
import credentialsManager from './credentials';

window.Alpine = Alpine;

// Register Alpine components
Alpine.data('credentialsManager', credentialsManager);

Alpine.start();
