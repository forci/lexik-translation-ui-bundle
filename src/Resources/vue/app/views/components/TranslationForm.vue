<template>

    <form class="form form-vertical" @submit.prevent="">

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="lxk_trans_unit_key" class="required">{{ labels.key }}</label>
                    <input type="text" id="lxk_trans_unit_key" class="form-control" v-model="key" v-validate="{ required: true }"
                           data-vv-name="key">
                    <p v-for="error in errors.collect('key')" v-show="errors.has('key')" class="text-danger">{{ error }}</p>
                </div>

                <div class="form-group">
                    <label for="lxk_trans_unit_domain" class="required">{{ labels.domain }}</label>
                    <select id="lxk_trans_unit_domain" class="form-control" v-model="domain" v-validate="{ required: true }"
                            data-vv-name="domain">
                        <option :value="domain" v-for="domain in domains">{{ domain }}</option>
                    </select>
                    <p v-for="error in errors.collect('domain')" v-show="errors.has('domain')" class="text-danger">{{ error }}</p>
                    <span class="text-danger"></span>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div v-for="(translation, index) in translations">
                        <label>{{ translation.locale }}</label>
                        <template v-if="translation.locale == 'en'">
                            <textarea class="form-control" v-model="translation.content" v-validate="{ required: true }"
                                      data-vv-name="defaultLocale" data-vv-as="default locale"></textarea>
                            <p v-for="error in errors.collect('defaultLocale')" v-show="errors.has('defaultLocale')" class="text-danger">{{ error
                                }}</p>
                        </template>
                        <template v-else>
                            <textarea class="form-control" v-model="translation.content"></textarea>
                        </template>
                        <input type="hidden" v-model="translation.locale">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>{{ labels.pageTitle }}</label>
        </div>

        <div class="form-group">
            <router-link to="/grid" class="btn btn-default">
                <i class="fas fa-arrow-left"></i>
                {{ labels.backToList }}
            </router-link>

            <div class="btn-group float-right">
                <button class="btn btn-primary" @click="save()">
                    {{ labels.save }}
                </button>
                <button class="btn btn-primary" @click="save(true)">
                    {{ labels.saveAdd }}
                </button>
            </div>
        </div>

        <input type="hidden" id="lxk_trans_unit__token" v-model="token">
    </form>

</template>

<script>

    export default {
        props: ['domains', 'locales', 'labels', 'defaultLocale', 'transUnitToken'],
        data() {
            return this.initialState();
        },
        methods: {
            initialState() {
                let translations = [];
                for (let i = 0; i < this.locales.length; i++) {
                    translations.push({
                        content: '',
                        locale: this.locales[i]
                    });
                }

                return {
                    key: '',
                    domain: 'messages', // default selected
                    translations: translations,
                    token: this.transUnitToken
                }
            },
            getSubmitData() {
                let data = {
                    key: this.key,
                    domain: this.domain,
                    _token: this.token
                };

                let transformed = [];
                for (let i = 0; i < this.translations.length; i++) {
                    transformed.push({
                        content: this.translations[i].content,
                        locale: this.translations[i].locale
                    });
                }

                data.translations = transformed;

                return {
                    lxk_trans_unit: data
                };
            },
            save(reload = false) {

                this.$validator.validateAll().then((result) => {
                    if (!result) {
                        this.$notify({
                            group: 'translation-new',
                            classes: 'vue-notification error',
                            title: 'Error',
                            text: this.labels.error
                        });

                        return;
                    }

                    let data = RequestParametersTransformer.serialize(this.getSubmitData());

                    axios.post(config.urls.create, data)
                        .then(_ => {
                            if (reload) {
                                this.$notify({
                                    group: 'translation-new',
                                    classes: 'vue-notification success',
                                    title: 'Success',
                                    text: this.labels.createSuccess
                                });
                                Object.assign(this.$data, this.initialState());
                            } else {
                                this.$router.push('/grid');
                            }
                        })
                        .catch(error => {
                            this.$notify({
                                duration: -1,
                                group: 'translation-new',
                                classes: 'vue-notification error',
                                title: 'Error',
                                text: error.response.data.errors[0]
                            });
                        });
                });

            }
        }
    }

</script>