import * as types from '../mutation-types'
import axios from 'axios'

// initial state
const state = {
    descriptions: {}
};

// getters
const getters = {
    descriptions: state => state.descriptions,
    getDescriptionByKey: state => {
        return (key) => state.descriptions[key];
    },
};

// actions
const actions = {
    load({commit}) {
        return new Promise((resolve, reject) => {
            axios.get('api/descriptions')
                .then(response => {
                    commit(types.DESCRIPTIONS_LOAD, {
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
    [types.DESCRIPTIONS_LOAD](state, {data}) {
        state.descriptions = data;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};