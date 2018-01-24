import Vue from 'vue'
import Vuex from 'vuex'
import VueRouter from 'vue-router'
import Notifications from 'vue-notification'
import axios from 'axios'
import VeeValidate from 'vee-validate'
import RequestParametersTransformer from './utilities/RequestParametersTransformer'

Vue.use(Vuex);
Vue.use(VueRouter);
Vue.use(Notifications);

Vue.use(VeeValidate);

window.Vue = Vue;

window.Vuex = Vuex;

window.axios = axios;

window.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'Content-Type': 'application/x-www-form-urlencoded'
};

window.RequestParametersTransformer = RequestParametersTransformer;

window.Event = new class {

    constructor() {
        this.vue = new Vue();
    }

    fire(event, data = null) {
        console.log('fired: ' + event);
        console.log(data);
        this.vue.$emit(event, data);
    }

    listen(event, callback) {
        this.vue.$on(event, callback);
    }
};