import Vue from 'vue';
import VueRouter from 'vue-router';
require('./bootstrap');
window.Vue = require('vue').default;
import router from './router.js';
import  BootstrapVue from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

Vue.use(BootstrapVue);
Vue.use(VueRouter);


//Vue.component('test', require('./components/Test.vue').default);

const app = new Vue({
    el: '#app',
    router
});
