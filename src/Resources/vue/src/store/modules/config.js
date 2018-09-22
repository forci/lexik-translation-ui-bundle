import * as types from '../mutation-types'
import axios from 'axios'

// initial state
const state = {
    config: {}
};

// getters
const getters = {
    defaultLocale: state => state.config.defaultLocale,
    locales: state => state.config.locales,
    editableLocales: state => state.config.editableLocales,
    domains: state => state.config.domains,
    inputType: state => state.config.inputType,
    transUnitToken: state => state.config.transUnitToken,
    recordsPerPage: state => state.config.recordsPerPage,
    canCreate: state => state.config.canCreate,
    labels: state => state.config.labels,
    config: state => state.config,
};

// actions
const actions = {
    load({commit}) {
        return new Promise((resolve, reject) => {
            axios.get('api/config')
                .then(response => {
                    commit(types.CONFIG_LOAD, {
                        data: response.data
                    });
                    resolve(response.data);
                })
                .catch(function (error) {
                    reject(error);
                });
        });
    },
};

// mutations
const mutations = {
    [types.CONFIG_LOAD](state, {data}) {
        state.config = data;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};