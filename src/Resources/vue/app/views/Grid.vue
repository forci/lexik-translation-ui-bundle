<template>

    <div>
        <notifications group="translation-grid"/>

        <div id="translation-grid">

            <div class="row">
                <div class="col-md-12">
                    <h3>
                        {{ labels.pageTitle }}
                        <button class="btn btn-primary float-right" @click="toggleShowAll">
                            {{ labels.toggleAllColumns }}
                        </button>
                    </h3>
                    <ul class="nav list-inline ml-auto">
                        <li class="locale">
                            <label for="toggle-id">
                                <input type="checkbox" id="toggle-id" v-model="showHide.id" @change="updateShowAll">
                                ID
                            </label>
                        </li>
                        <li class="locale">
                            <label for="toggle-domain">
                                <input type="checkbox" id="toggle-domain" v-model="showHide.domain"
                                       @change="updateShowAll">
                                {{ labels.domain }}
                            </label>
                        </li>
                        <li class="locale">
                            <label for="toggle-key">
                                <input type="checkbox" id="toggle-key" v-model="showHide.key" @change="updateShowAll">
                                {{ labels.key }}
                            </label>
                        </li>
                        <li class="locale" v-for="locale in locales">
                            <label>
                                <input type="checkbox" v-model="showHide[locale]" @change="updateShowAll">
                                {{ locale }}
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row margin-row">
                <div class="col-md-12">
                    <paginate v-show="pageCount > 1"
                              :pageCount="pageCount"
                              :clickHandler="setCurrentPage"
                              :prevText="'<<'"
                              :nextText="'>>'"
                              :initialPage="currentPage - 1"
                              :forcePage="currentPage - 1"
                              :container-class="'pagination'"
                              :page-class="'page-item'"
                              :page-link-class="'page-link'"
                              :prev-class="'page-item'"
                              :prev-link-class="'page-link'"
                              :next-class="'page-item'"
                              :next-link-class="'page-link'">
                    </paginate>
                    <translation-table :ref="'translation-table'"
                                       :locales="locales"
                                       :labels="labels"
                                       :page="currentPage"
                                       :columns="showHide"
                                       :inputType="inputType">
                    </translation-table>
                    <paginate v-show="pageCount > 1"
                              :pageCount="pageCount"
                              :clickHandler="setCurrentPage"
                              :prevText="'<<'"
                              :nextText="'>>'"
                              :initialPage="currentPage - 1"
                              :forcePage="currentPage - 1"
                              :container-class="'pagination'"
                              :page-class="'page-item'"
                              :page-link-class="'page-link'"
                              :prev-class="'page-item'"
                              :prev-link-class="'page-link'"
                              :next-class="'page-item'"
                              :next-link-class="'page-link'">
                    </paginate>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

    import {mapGetters} from 'vuex'
    import Paginate from 'vuejs-paginate'
    import TranslationTable from './components/TranslationTable.vue';

    export default {
        data() {
            return {
                locales: config.locales,
                labels: config.labels,
                inputType: config.inputType,
                profilerOn: false,
                showHideColumnsOn: false,
                sharedMsg: null,
                showAll: true,
                showHide: this.initShowHide()
            };
        },
        computed: mapGetters({
            total: 'total',
            pageCount: 'pageCount',
            currentPage: 'currentPage'
        }),
        components: {
            TranslationTable,
            Paginate
        },
        mounted() {
            this.$store.dispatch('loadTranslations').then(() => {
                this.$nextTick(() => {
                    this.$refs['translation-table'].focusCurrentRow();
                })
            });
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
                for (let i = 0; i < config.locales.length; i++) {
                    dynamicProps[config.locales[i]] = config.locales[i] == config.defaultLocale;
                }

                let staticProps = {
                    id: false,
                    domain: true,
                    key: true
                };

                return Object.assign({}, dynamicProps, staticProps);
            },
            setCurrentPage(page) {
                this.$store.dispatch('loadTranslations', {page: page});
            }
        }
    }

</script>