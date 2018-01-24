import VueRouter from 'vue-router';
import Overview from './views/Overview.vue'
import Grid from './views/Grid.vue'
import New from './views/New.vue'

let routes = [
    {
        path: '*',
        redirect: '/overview'
    },
    {
        path: '/overview',
        component: Overview
    },
    {
        path: '/grid',
        component: Grid
    },
    {
        path: '/new',
        component: New
    }
];

export default new VueRouter({
    // mode: 'history',
    routes,
    linkActiveClass: 'active'
});