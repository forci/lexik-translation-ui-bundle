import * as actions from './actions'
import statistics from './modules/statistics'
import translations from './modules/translations'
import createLogger from '../utilities/Logger'

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
    actions,
    // getters,
    modules: {
        translations,
        statistics
    },
    strict: debug,
    plugins: debug ? [createLogger()] : []
})