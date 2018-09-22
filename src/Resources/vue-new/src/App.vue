<template>

    <div>
        <notifications group="index"/>

        <template v-if="configLoaded">
            <b-navbar toggleable="md" type="dark" variant="info">

                <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>

                <b-navbar-brand :to="{ name: 'overview' }">Forci Lexik Translation UI</b-navbar-brand>

                <b-collapse is-nav id="nav_collapse">

                    <b-navbar-nav>
                        <b-nav-item :to="{ name: 'overview' }">{{ labels.pageTitleOverview }}</b-nav-item>
                        <b-nav-item :to="{ name: 'grid' }">{{ labels.showGrid }}</b-nav-item>
                        <b-nav-item :to="{ name: 'new' }" v-if="canCreate">{{ labels.addTranslation }}</b-nav-item>
                    </b-navbar-nav>

                    <b-navbar-nav class="ml-auto">
                        <b-button variant="primary" @click="invalidateCache" class="my-2 my-sm-0" type="button">{{ labels.invalidateCache }}</b-button>
                    </b-navbar-nav>

                </b-collapse>
            </b-navbar>

            <br/>

            <b-container fluid>
                <router-view/>
            </b-container>
        </template>
        <template v-else>
            <b-container>
                <b-jumbotron bg-variant="info" text-variant="white" header="Loading configuration" lead="Please wait"/>
            </b-container>
        </template>
    </div>

</template>

<script>

    import {mapGetters} from 'vuex'

    export default {
        data() {
            return {
                configLoaded: false
            };
        },
        mounted() {
            this.$store.dispatch('config/load').then(() => {
                this.configLoaded = true;
                this.$store.dispatch('descriptions/load');
            });
        },
        computed: {
            ...mapGetters('config', {
                locales: 'locales',
                labels: 'labels',
                transUnitToken: 'transUnitToken',
                domains: 'domains',
                defaultLocale: 'defaultLocale',
                canCreate: 'canCreate'
            })
        },
        methods: {
            invalidateCache() {
                this.$store.dispatch('translations/clearCache')
                    .then(message => {
                        this.$notify({
                            group: 'index',
                            classes: 'vue-notification success',
                            title: 'Success',
                            text: message
                        });
                    })
                    .catch(() => {
                        this.$notify({
                            group: 'index',
                            classes: 'vue-notification error',
                            title: 'Error',
                            text: this.labels.error
                        });
                    });
            }
        }
    }

</script>