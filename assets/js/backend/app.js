import Alpine from 'alpinejs';
import accountIDComponent from './components/accountIDComponent';
import pluginList from './components/pluginList';

window.Alpine = Alpine;

window.accountIDComponent = accountIDComponent.instance;
window.pluginList = pluginList.instance;

Alpine.start();
