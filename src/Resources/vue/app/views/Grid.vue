<template>

    <div>
        <notifications group="translation-grid"/>
        <!--<div class="page-header">-->
        <!--<h1>-->
        <!--{{ labels.pageTitle }}-->
        <!--<div class="pull-right">-->
        <!--<router-link to="/new" class="btn btn-success">-->
        <!--<span class="glyphicon glyphicon-plus"></span>-->
        <!--{{ labels.newTranslation }}-->
        <!--</router-link>-->
        <!--<router-link to="/overview" class="btn btn-primary">-->
        <!--<span class="glyphicon glyphicon-tasks"></span>-->
        <!--{{ labels.pageTitleOverview }}-->
        <!--</router-link>-->
        <!--</div>-->
        <!--</h1>-->
        <!--</div>-->

        <div id="translation-grid">
            <!--<div>-->
            <!--<div class="row">-->
            <!--<div class="col-md-2">-->
            <!--<label>translations.data_source:&nbsp;</label>-->
            <!--<div class="btn-group" role="group">-->
            <!--<button type="button" class="btn" :class="profilerOn ? 'btn-default' : 'btn-info'" @click="profilerOn=false">-->
            <!--{{ labels.allTranslations }}-->
            <!--</button>-->
            <!--<button type="button" class="btn" :class="profilerOn ? 'btn-info' : 'btn-default'" @click="profilerOn=true">-->
            <!--{{ labels.profiler }}-->
            <!--</button>-->
            <!--</div>-->
            <!--</div>-->
            <!--<div class="col-md-5" v-if="profilerOn">-->
            <!--<label>{{ labels.latestProfiles }}:&nbsp;</label>-->
            <!--<select class="form-control">-->
            <!--<option value="" selected="selected"></option>-->
            <!--</select>-->
            <!--</div>-->
            <!--<div class="col-md-2" v-if="profilerOn">-->
            <!--<label>{{ labels.profile }}:&nbsp;</label>-->
            <!--<input type="text" class="form-control">-->
            <!--</div>-->
            <!--</div>-->

            <!--<hr>-->
            <!--</div>-->


            <!--<div class="row margin-row grid-action-bar">-->
            <!--<div class="col-md-12">-->
            <!--<a class="btn btn-default btn-sm btn-manage-col" @click="toggleShowHideColumns">-->
            <!--<span class="glyphicon glyphicon-eye-close"></span>-->
            <!--{{ labels.hideCol }}-->
            <!--</a>-->
            <!--</div>-->
            <!--</div>-->

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
                                <input type="checkbox" id="toggle-domain" v-model="showHide.domain" @change="updateShowAll">
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
                    <translation-table :locales="locales"
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
            this.$store.dispatch('loadTranslations');
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
                    dynamicProps[config.locales[i]] = true;
                }

                let staticProps = {
                    id: true,
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