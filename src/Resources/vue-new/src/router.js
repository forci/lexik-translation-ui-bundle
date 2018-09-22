import Vue from 'vue'
import Router from 'vue-router'
import Overview from './views/Overview.vue'
import Grid from './views/Grid.vue'
import New from './views/New.vue'

Vue.use(Router)

let routes = [
    {
        path: '*',
        redirect: '/overview'
    },
    {
        name: 'overview',
        path: '/overview',
        component: Overview
    },
    {
        name: 'grid',
        path: '/grid',
        component: Grid
    },
    {
        name: 'new',
        path: '/new',
        component: New
    }
]

export default new Router({
    // mode: 'history',
    routes,
    linkActiveClass: 'active'
})