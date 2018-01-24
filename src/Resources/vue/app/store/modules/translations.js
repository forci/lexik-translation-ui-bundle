import * as types from '../mutation-types'

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
    pageCount: state => Math.ceil(state.total / config.recordsPerPage),
    currentPage: state => state.currentPage
};

// actions
const actions = {
    //
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
    state,
    getters,
    actions,
    mutations
};