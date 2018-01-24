import './bootstrap';
import router from './routes.js';
import store from './store'
import Index from './views/Index.vue'

new Vue({

    el: '#app',
    store,
    router,
    components: {
        Index
    }

});