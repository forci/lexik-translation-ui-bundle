import * as types from '../mutation-types'

// initial state
const state = {
    overviewData: null
};

// getters
const getters = {
    getOverviewData: state => state.overviewData
};

// actions
const actions = {
    loadStatistics({commit}) {
        return new Promise((resolve, reject) => {
            axios.get(config.urls.overviewData)
                .then(response => {
                    commit(types.STATISTICS_LOAD, {
                        data: response.data
                    });
                    resolve();
                })
                .catch(function (error) {
                    reject(error);
                });
        });
    },
};

// mutations
const mutations = {
    [types.STATISTICS_LOAD](state, {data}) {
        state.overviewData = data;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};