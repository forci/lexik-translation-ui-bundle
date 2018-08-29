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
                {{ locale }} -
                <label class="label">
                    <input type="checkbox" v-model="editableColumns[locale]">
                    Editable
                </label>
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
                             :translation-id="translation._id"
                             :columns="columns"
                             :editableColumns="editableColumns"
                             :inputType="inputType"
                             v-on:changeTranslationRow="onChangeTranslationRow"
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
        props: ['locales', 'labels', 'page', 'columns', 'editableColumns', 'inputType'],
        data() {
            return {
                currentRow: 0,
                defaultLocale: config.defaultLocale,
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

                this.$store
                    .dispatch('loadTranslations', {
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

                this.$store
                    .dispatch('loadTranslations', {
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
            focusCurrentRow() {
                let row = this.$refs['translation-row-' + this.$data.currentRow];
                if (row.length) {
                    row[0].setMode('edit');
                    this.$nextTick(function () {
                        row[0].focusField();
                    })
                }
            },
            onChangeTranslationRow(data, test) {
                this.$data.defaultLocale = data.locale;

                // if next row - open
                if ((data.index + 1) < this.translations.length) {
                    this.$data.currentRow = data.index + 1;
                    this.focusCurrentRow();
                    return;
                }

                // if last page - show toast
                if (this.$store.getters.pageCount === this.page) {
                    this.$data.currentRow = null;
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
                        this.$data.currentRow = 0;
                        this.focusCurrentRow();
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            onSetDefaultLocale(data, test) {
                this.$data.defaultLocale = data.locale;
            },
        }
    }

</script>