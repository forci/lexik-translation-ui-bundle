<template>

    <table class="table table-bordered table-striped grid-table" ref="translationTable">
        <thead>
        <tr>
            <th class="text-center" v-show="columnVisible('id')">
                ID
                &nbsp;
                <i class="fas fa-chevron-down" @click="sortBy('_id')"></i>
            </th>
            <th class="text-center" v-show="columnVisible('domain')">
                {{ labels.domain }}
                &nbsp;
                <i class="fas fa-chevron-down" @click="sortBy('_domain')"></i>
            </th>
            <th class="text-center" v-show="columnVisible('key')">
                {{ labels.key }}
                &nbsp;
                <i class="fas fa-chevron-down" @click="sortBy('_key')"></i>
            </th>

            <th class="text-center" v-for="locale in locales" v-show="columnVisible(locale)">
                {{ locale }}
            </th>

            <th></th>
        </tr>

        <tr>
            <th v-show="columnVisible('id')"></th>
            <th v-show="columnVisible('domain')">
                <input type="text" class="form-control input-sm" v-on:keyup="search('_domain', $event.target.value)"/>
            </th>
            <th v-show="columnVisible('key')">
                <input type="text" class="form-control input-sm" v-on:keyup="search('_key', $event.target.value)"/>
            </th>
            <th v-for="locale in locales" v-show="columnVisible(locale)">
                <input type="text" class="form-control input-sm" v-on:keyup="search(locale, $event.target.value)"/>
            </th>

            <th></th>
        </tr>


        </thead>

        <tbody>

        <template v-for="(translation, index) in translations">
            <translation-row :ref="'translation-row-' + index"
                             :key="'row' + index"
                             :index="index"
                             :locales="locales"
                             :defaultLocale="defaultLocale"
                             :labels="labels"
                             :mode="modes[index]"
                             :translation-id="translation._id"
                             :columns="columns"
                             :inputType="inputType"
                             v-on:toggleTranslationRow="onToggleTranslationRow"
                             v-on:changeTranslationRow="onChangeTranslationRow"
                             v-on:deleteTranslationRow="onDeleteTranslationRow"
                             v-on:setDefaultLocale="onSetDefaultLocale">
            </translation-row>
        </template>

        </tbody>
    </table>

</template>

<script>

    import {mapGetters} from 'vuex'
    import TranslationRow from './TranslationRow.vue'

    export default {
        props: ['locales', 'labels', 'page', 'columns', 'inputType'],
        data() {
            return {
                rowInEditMode: 0,
                rowInDeleteMode: null,
                defaultLocale: null,
                currentSort: {
                    sortKey: '_id',
                    order: 'asc'
                },
                currentSearch: {}
            };
        },
        computed: {
            ...mapGetters({
                translations: 'getAllTranslations'
            }),
            modes() {
                let modesArr = Array.apply(null, new Array(this.translations.length)).map(String.prototype.valueOf, "read");
                if (this.rowInEditMode !== null) {
                    modesArr[this.rowInEditMode] = 'edit';
                }
                if (this.rowInDeleteMode !== null) {
                    modesArr[this.rowInDeleteMode] = 'delete';
                }
                return modesArr;
            }
        },
        components: {
            TranslationRow
        },
        methods: {
            columnVisible(key) {
                return this.columns.all || this.columns[key];
            },
            showError(error) {
                this.$notify({
                    duration: -1,
                    group: 'translation-grid',
                    classes: 'vue-notification error',
                    title: 'Error',
                    text: error
                });
            },
            sortBy(sortKey) {
                let order = 'asc';
                if (this.currentSort.sortKey === sortKey) {
                    order = this.currentSort.order === 'asc' ? 'desc' : 'asc';
                }

                this.$store.dispatch('loadTranslations', {
                    page: this.page,
                    sort: sortKey,
                    order: order,
                    filter: this.currentSearch
                })
                    .then(_ => {
                        this.$data.currentSort = {
                            sortKey: sortKey,
                            order: order
                        };
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            search(key, value) {
                let filter = Object.assign({}, this.currentSearch, {[key]: value, _search: true});

                this.$store.dispatch('loadTranslations', {
                    page: this.page,
                    sort: this.currentSort.sortKey,
                    order: this.currentSort.order,
                    filter: filter
                })
                    .then(_ => {
                        this.$data.page = 1;
                        this.$data.currentSort = {
                            sortKey: '_id',
                            order: 'asc'
                        };
                        this.$data.currentSearch = filter;
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            onChangeTranslationRow(data) {
                this.$data.defaultLocale = data.locale;
                // if next row - open
                if ((data.index + 1) < this.translations.length) {
                    this.$data.rowInEditMode = data.index + 1;
                    return;
                }

                // if last page - show toast
                if (this.$store.getters.pageCount === this.page) {
                    this.$data.rowInEditMode = null;
                    window.scrollTo(0, 0);
                    this.$toast(this.$refs.translationTable, this.labels.allDone, 'success', 2000, 100);
                    return;
                }

                // else load next page
                this.$store.dispatch('loadTranslations', {
                    page: this.page + 1,
                    sort: this.currentSort.sortKey,
                    order: this.currentSort.order,
                    filter: this.currentSearch
                })
                    .then(_ => {
                        this.$data.rowInEditMode = 0;
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            onSetDefaultLocale(data) {
                this.$data.defaultLocale = data.locale;
            },
            onDeleteTranslationRow() {
                this.$data.rowInDeleteMode = null;
            },
            onToggleTranslationRow(data) {
                switch (data.mode) {
                    case 'edit':
                        if (this.rowInEditMode === null || this.rowInEditMode !== data.index) {
                            this.$data.rowInEditMode = data.index;
                            return;
                        }
                        this.$data.rowInEditMode = null;
                        break;
                    case 'delete':
                        if (this.rowInDeleteMode === null || this.rowInDeleteMode !== data.index) {
                            this.$data.rowInDeleteMode = data.index;
                            return;
                        }
                        this.$data.rowInDeleteMode = null;
                        break;
                    case 'read':
                        this.$data.rowInEditMode = null;
                        this.$data.rowInDeleteMode = null;
                }
            }
        }
    }

</script>