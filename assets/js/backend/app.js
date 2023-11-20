import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import accountIDComponent from './components/accountIDComponent';
import selectLang from './components/selectLang';
import pluginList from './components/pluginList';
import noticeComponent from './components/noticeComponent';

window.Alpine = Alpine;

window.accountIDComponent = accountIDComponent.instance;
window.pluginList = pluginList.instance;
window.noticeComponent = noticeComponent.instance;
window.selectLang = selectLang.instance;

Alpine.plugin( persist );
Alpine.start();
