import Alpine from 'alpinejs';
import accountIDComponent from './components/accountIDComponent';

window.Alpine = Alpine;

window.accountIDComponent = accountIDComponent.instance;

Alpine.start();
