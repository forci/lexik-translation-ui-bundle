import * as types from '../mutation-types'
import axios from 'axios'
import RequestParametersTransformer from "../../utilities/RequestParametersTransformer";

// TODO useful - move out
Array.prototype.findOneBy = function (prop, val) {
    let index = this.map((e) => {
        return e[prop];
    }).indexOf(val);

    return this[index];
};
Array.prototype.findIndexBy = function (prop, val) {
    return this.map((e) => {
        return e[prop];
    }).indexOf(val);
};

// initial state
const state = {
    all: [],
    total: null,
    currentPage: 1
};

// getters
const getters = {
    getAllTranslations: state => state.all,
    getTranslationById: state => {
        return (id) => state.all.findOneBy('_id', id);
    },
    total: state => state.total,
    pageCount(state, getters, rootState, rootGetters) {
        return Math.ceil(state.total / rootGetters['config/recordsPerPage'])
        // console.log(arguments)
    },
    // pageCount: (state, getters, rootState, rootGetters) => Math.ceil(state.total / rootGetters['config'].recordsPerPage),
    // pageCount: state => Math.ceil(state.total / config.recordsPerPage),
    currentPage: state => state.currentPage
};

// actions
const actions = {
    load({commit, rootGetters}, data) {
        return new Promise((resolve, reject) => {

            if (!data) {
                data = {
                    page: 1,
                    sort: '_id',
                    order: 'asc',
                    filter: null
                };
            }

            let params = {
                page: data.page,
                rows: rootGetters['config/recordsPerPage'],
                // rows: config.recordsPerPage,
                sidx: data.sort,
                sord: data.order
            };

            if (data.filter) {
                params = Object.assign({}, params, data.filter);
            }

            axios.get('api/translations', {params: params})
                .then(response => {
                    // or pass data object, if params not needed..
                    commit(types.TRANSLATION_LIST, {
                        translations: response.data.translations,
                        total: response.data.total,
                        page: params.page
                    });
                    resolve();
                })
                .catch(function (error) {
                    reject(error);
                });

        });
    },
    update({commit}, data) {
        return new Promise((resolve, reject) => {

            let url = 'api/translations/' + data.translation._id;
            let requestData = RequestParametersTransformer.serialize(data.translation);

            axios.put(url, requestData)
                .then((response) => {
                    commit(types.TRANSLATION_UPDATE, {
                        translation: response.data
                    });
                    resolve(response.data);
                })
                .catch(function (error) {
                    reject(error);
                });

        });
    },
    delete({commit}, data) {
        return new Promise((resolve, reject) => {

            let url = 'api/translations/' + data.translation._id;

            axios.delete(url)
                .then(() => {
                    commit(types.TRANSLATION_DELETE, {
                        id: data.translation._id
                    });
                    resolve(data.translation._id);
                })
                .catch(function (error) {
                    reject(error);
                });

        });
    },
    deleteLocation({commit}, data) {
        return new Promise((resolve, reject) => {

            let url = 'api/translations/' + data.translation._id + '/' + data.locale;

            axios.delete(url)
                .then(() => {
                    let updatedTranslation = JSON.parse(JSON.stringify(data.translation));
                    updatedTranslation[data.locale] = '';

                    commit(types.TRANSLATION_UPDATE, {
                        translation: updatedTranslation
                    });

                    resolve(data.translation._id, data.locale);
                })
                .catch(function (error) {
                    reject(error);
                });

        });
    },
    clearCache() {
        return new Promise((resolve, reject) => {

            axios.get('api/invalidate-cache')
                .then((response) => {
                    resolve(response.data.message);
                })
                .catch(function (error) {
                    reject(error);
                });

        });
    },
};

// mutations
const mutations = {
    [types.TRANSLATION_LIST](state, {translations, total, page}) {
        state.all = translations;
        state.total = total;
        state.currentPage = page;
    },

    [types.TRANSLATION_CREATE](state, {translation}) {
        state.all.push(translation);
    },

    [types.TRANSLATION_SYNC](state, {id, field, value}) {
        let index = state.all.findIndexBy('_id', id);
        let replace = Object.assign({}, state.all[index], {
            [field]: value
        });
        state.all.splice(index, 1, replace);
    },

    [types.TRANSLATION_UPDATE](state, {translation}) {
        let index = state.all.findIndexBy('_id', translation._id);
        state.all.splice(index, 1, translation);
    },

    [types.TRANSLATION_DELETE](state, {id}) {
        let index = state.all.findIndexBy('_id', id);
        state.all.splice(index, 1);
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
};