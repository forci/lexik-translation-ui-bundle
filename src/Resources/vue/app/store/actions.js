import * as types from './mutation-types'

export const loadTranslations = ({commit}, data) => {
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
            rows: config.recordsPerPage,
            sidx: data.sort,
            sord: data.order
        };

        if (data.filter) {
            params = Object.assign({}, params, data.filter);
        }

        axios.get(config.urls.list, {params: params})
            .then(response => {
                // or pass data object, if params not needed..
                commit(types.TRANSLATION_LIST, {
                    translations: response.data.translations,
                    total: response.data.total,
                    page: data.page
                });
                resolve();
            })
            .catch(function (error) {
                reject(error);
            });

    });
};

export const updateTranslation = ({commit}, data) => {
    return new Promise((resolve, reject) => {

        let url = config.urls.update.replace('-id-', data.translation._id);
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
};

export const deleteTranslation = ({commit}, data) => {
    return new Promise((resolve, reject) => {

        let url = config.urls.delete.replace('-id-', data.translation._id);

        axios.delete(url)
            .then((_) => {
                commit(types.TRANSLATION_DELETE, {
                    id: data.translation._id
                });
                resolve(data.translation._id);
            })
            .catch(function (error) {
                reject(error);
            });

    });
};

export const deleteTranslationLocation = ({commit}, data) => {
    return new Promise((resolve, reject) => {

        let url = config.urls.delete.replace('-id-', data.translation._id) + '/' + data.locale;

        axios.delete(url)
            .then((_) => {
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
};

export const clearTranslationCache = () => {
    return new Promise((resolve, reject) => {

        axios.get(config.urls.invalidateCache)
            .then((response) => {
                resolve(response.data.message);
            })
            .catch(function (error) {
                reject(error);
            });

    });
};