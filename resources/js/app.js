require('./bootstrap');

window.Vue = require('vue')
import router from './router'
import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';
Vue.use(ViewUI);

import common from './common'
import jsonToHtml from './jsonToHtml'
import store from './store'
Vue.mixin(common)
Vue.mixin(jsonToHtml)

import Editor from 'vue-editor-js'
Vue.use(Editor)


Vue.component('main-app', require('./components/MainApp.vue').default)

const app = new Vue({
    el : '#app',
    router,
    store
})