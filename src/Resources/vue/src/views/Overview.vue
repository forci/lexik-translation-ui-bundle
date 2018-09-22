<template>

    <div>
        <notifications group="translation-overview"/>
        <div class="float-right">
            <router-link to="/grid" class="btn btn-primary">
                <i class="fas fa-th"></i>
                {{ labels.showGrid }}
            </router-link>
        </div>
        <h3>{{ labels.pageTitleOverview }}</h3>
        <hr/>
        <p v-if="dataLoaded" v-text="latestTranslationMessage"></p>
        <div id="translation-overview" class="table-responsive">
            <overview-table
                    :locales="locales"
                    :labels="labels"
                    :overviewData="overviewData"
                    v-if="dataLoaded">
            </overview-table>
            <div class="alert alert-info" v-else>{{ labels.overviewNoStats }}</div>
        </div>
    </div>

</template>

<script>

    import {mapGetters} from 'vuex'
    import OverviewTable from '../components/OverviewTable.vue';

    export default {
        computed: {
            ...mapGetters('config', {
                locales: 'locales',
                labels: 'labels'
            }),
            ...mapGetters('statistics', {
                overviewData: 'overviewData'
            }),
            dataLoaded() {
                return this.overviewData && this.overviewData.stats;
            },
            latestTranslationMessage() {
                if (this.dataLoaded) {
                    let date = new Date(this.overviewData.latestTrans.date);
                    let dateString = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " +
                        date.getHours() + ":" + date.getMinutes();

                    return this.labels.latestTranslation.replace('%date%', dateString);
                }
                return '';
            }
        },
        components: {
            OverviewTable
        },
        mounted() {
            console.log('adssa');
            this.$store.dispatch('statistics/load').then().catch((error) => {
                this.$notify({
                    duration: -1,
                    group: 'translation-overview',
                    classes: 'vue-notification error',
                    title: 'Error',
                    text: error
                });
            });
        }
    }

</script>
