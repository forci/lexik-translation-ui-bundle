<template>

    <div>
        <notifications group="index"/>
        <div class="blog-masthead">
            <div class="container">
                <nav class="blog-nav">
                    <router-link to="/overview" class="blog-nav-item">
                        <i class="fas fa-list"></i>
                        {{ labels.pageTitleOverview }}
                    </router-link>
                    <router-link to="/grid" class="blog-nav-item">
                        <i class="fas fa-th"></i>
                        {{ labels.showGrid }}
                    </router-link>
                    <router-link to="/new" class="blog-nav-item">
                        <i class="fas fa-plus"></i>
                        {{ labels.addTranslation }}
                    </router-link>
                    <a role="button" class="blog-nav-item float-right" @click="invalidateCache">
                        <i class="fas fa-sync"></i>
                        {{ labels.invalidateCache }}
                    </a>
                </nav>
            </div>
        </div>

        <div class="container">
            <router-view/>
        </div>
    </div>

</template>

<script>

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
        methods: {
            invalidateCache() {
                this.$store.dispatch('clearTranslationCache')
                    .then(message => {
                        this.$notify({
                            group: 'index',
                            classes: 'vue-notification success',
                            title: 'Success',
                            text: message
                        });
                    })
                    .catch(error => {
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