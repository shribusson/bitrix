import './scss/main.scss';
import store from './store/store';

import Vue from 'vue';

window.Vue = Vue;

if(process.env.NODE_ENV == 'production') {
    Vue.config.productionTip = false;
    Vue.config.devtools = false;
    Vue.config.debug = false;
    Vue.config.silent = true;
}

Vue.component('orgstructure', require('./components/Orgstructure.vue').default);
//Vue.component('vue-tree', require('./components/VueTree.vue').default);
Vue.component('preloader', require('./components/Preloader.vue').default);
//Vue.component('user', require('./components/User.vue').default)

window.onload = function () {
    let appNode = document.getElementById('app');
    if (appNode) {
        window.Vue = new Vue({
            el: '#app',
            store: store
        });
    }
}