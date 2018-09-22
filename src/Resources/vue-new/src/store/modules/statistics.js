import * as types from '../mutation-types'
import axios from 'axios'

// initial state
const state = {
    overviewData: null
};

// getters
const getters = {
    overviewData: state => state.overviewData
};

// actions
const actions = {
    load({commit}) {
        return new Promise((resolve, reject) => {
            axios.get('api/overview')
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
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};