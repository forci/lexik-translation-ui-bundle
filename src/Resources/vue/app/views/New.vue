<template>

    <div>
        <notifications group="translation-new"/>
        <h3>{{ labels.addTranslation }}</h3>
        <hr/>

        <translation-form :domains="domains" :locales="locales" :labels="labels" :transUnitToken="transUnitToken" :defaultLocale="defaultLocale"></translation-form>
    </div>

</template>

<script>

    import TranslationForm from './components/TranslationForm.vue';

    export default {
        data() {
            return {
                locales: config.locales,
                labels: config.labels,
                transUnitToken: config.transUnitToken,
                domains: config.domains,
                defaultLocale: config.defaultLocale
            };
        },
        components: {
            TranslationForm
        }
    }

</script>