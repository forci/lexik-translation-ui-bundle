import Vue from 'vue'
import Vuex from 'vuex'
import config from './modules/config'
import descriptions from './modules/descriptions'
import statistics from './modules/statistics'
import translations from './modules/translations'
import createLogger from '../utilities/Logger'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
    modules: {
        config,
        descriptions,
        translations,
        statistics
    },
    strict: debug,
    plugins: debug ? [createLogger()] : []
})