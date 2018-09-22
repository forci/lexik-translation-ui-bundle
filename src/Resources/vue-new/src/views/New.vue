<template>

    <div>
        <notifications group="translation-new"/>
        <h3>{{ labels.addTranslation }}</h3>
        <hr/>

        <translation-form :domains="domains" :locales="locales" :labels="labels" :transUnitToken="transUnitToken" :defaultLocale="defaultLocale"></translation-form>
    </div>

</template>

<script>

    import {mapGetters} from 'vuex'
    import TranslationForm from '../components/TranslationForm.vue'

    export default {
        computed: {
            ...mapGetters('config', {
                locales: 'locales',
                labels: 'labels',
                transUnitToken: 'transUnitToken',
                domains: 'domains',
                defaultLocale: 'defaultLocale',
            }),
        },
        components: {
            TranslationForm
        }
    }

</script>
