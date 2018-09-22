<template>

    <div>
        <notifications group="translation-grid"/>

        <div id="translation-grid" v-if="mounted">

            <div class="row">
                <div class="col-md-12">
                    <h3>
                        {{ labels.pageTitle }}
                        <button class="btn btn-primary float-right" @click="toggleShowAll">
                            {{ labels.toggleAllColumns }}
                        </button>
                    </h3>
                    <b-form-group label="Use these to show or hide columns in the table below">
                        <b-form-checkbox v-model="showHide.id"
                                         @change="updateShowAll">
                            ID
                        </b-form-checkbox>
                        <b-form-checkbox v-model="showHide.domain"
                                         @change="updateShowAll">
                            {{ labels.domain }}
                        </b-form-checkbox>
                        <b-form-checkbox v-model="showHide.key"
                                         @change="updateShowAll">
                            {{ labels.key }}
                        </b-form-checkbox>
                        <b-form-checkbox v-for="locale in locales" :key="locale"
                                         v-model="showHide[locale]"
                                         @change="updateShowAll">
                            {{ locale }}
                        </b-form-checkbox>
                    </b-form-group>
                </div>
            </div>

            <div class="row margin-row">
                <div class="col-md-12">
                    <b-pagination
                            @input="setCurrentPage"
                            :total-rows="total"
                            v-model="currentPage"
                            :per-page="recordsPerPage">
                    </b-pagination>
                    <translation-table :ref="'translation-table'"
                                       :locales="locales"
                                       :labels="labels"
                                       :page="currentPage"
                                       :columns="showHide"
                                       :editableColumns="editableLocales"
                                       :inputType="inputType">
                    </translation-table>
                    <b-pagination
                            @input="setCurrentPage"
                            :total-rows="total"
                            v-model="currentPage"
                            :per-page="recordsPerPage">
                    </b-pagination>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

    import {mapGetters} from 'vuex'
    import TranslationTable from '../components/TranslationTable.vue';

    export default {
        data() {
            return {
                profilerOn: false,
                showHideColumnsOn: false,
                sharedMsg: null,
                showAll: true,
                showHide: null,
                editableLocales: null,
                mounted: false,
                currentPage: 1
            };
        },
        computed: {
            ...mapGetters('config', {
                locales: 'locales',
                labels: 'labels',
                inputType: 'inputType',
                recordsPerPage: 'recordsPerPage',
                defaultLocale: 'defaultLocale',
            }),
            ...mapGetters('translations', {
                total: 'total',
                pageCount: 'pageCount',
                translationsPage: 'currentPage'
            }),
        },
        watch: {
            translationsPage(newVal) {
                // Keeps Pagination up to date when editing skips to the next page
                this.currentPage = newVal;
            }
        },
        components: {
            TranslationTable
        },
        mounted() {
            this.showHide = this.initShowHide()
            this.editableLocales = this.initEditableLocales()
            this.$store.dispatch('translations/load').then(() => {
                this.$nextTick(() => {
                    this.$refs['translation-table'].focusCurrentRow();
                })
            });
            this.mounted = true;
        },
        methods: {
            toggleShowAll() {
                this.showAll = !this.showAll;

                for (const key of Object.keys(this.showHide)) {
                    this.showHide[key] = this.showAll;
                }
            },
            updateShowAll() {
                let allVisible = true;
                for (const key of Object.keys(this.showHide)) {
                    if (!this.showHide[key]) {
                        allVisible = false;
                        break;
                    }
                }

                this.showAll = allVisible;
            },
            toggleShowHideColumns() {
                this.$data.showHideColumnsOn = !this.showHideColumnsOn;
            },
            initShowHide() {
                let dynamicProps = {};
                for (let i = 0; i < this.locales.length; i++) {
                    dynamicProps[this.locales[i]] = this.locales[i] === this.defaultLocale;
                }

                let staticProps = {
                    id: false,
                    domain: true,
                    key: true
                };

                return Object.assign({}, dynamicProps, staticProps);
            },
            initEditableLocales() {
                let dynamicProps = {};
                for (let i = 0; i < this.locales.length; i++) {
                    dynamicProps[this.locales[i]] = this.locales[i] === this.defaultLocale;
                }

                return dynamicProps;
            },
            setCurrentPage(page) {
                this.$store.dispatch('translations/load', {page: page});
            }
        }
    }

</script>
