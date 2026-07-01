import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import collapse from '@alpinejs/collapse';
import accountIDComponent from './components/accountIDComponent';
import imageUploadComponent from './components/imageUploadComponent';
import selectLang from './components/selectLang';
import pluginList from './components/pluginList';
import selectComponent from './components/selectComponent';
import noticeComponent from './components/noticeComponent';

window.Alpine = Alpine;

window.accountIDComponent = accountIDComponent.instance;
window.imageUploadComponent = imageUploadComponent.instance;
window.pluginList = pluginList.instance;
window.noticeComponent = noticeComponent.instance;
window.selectLang = selectLang.instance;

Alpine.data('SelectComponent', selectComponent);

Alpine.plugin( persist );
Alpine.plugin( collapse );

Alpine.start();
