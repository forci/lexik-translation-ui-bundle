<template>

    <tr>
        <td v-show="columnVisible('id')">
            <span>{{ translation._id }}</span>
        </td>
        <td v-show="columnVisible('domain')">
            <span>{{ translation._domain }}</span>
        </td>
        <td class="grid-elipsis" v-show="columnVisible('key')" :title="translation._key">
            <span>{{ translation._key }}</span>
            <div class="text-center" v-if="mode === 'delete'">
                <button type="button" class="btn btn-link" @click="remove">
                    <i class="fas fa-trash text-danger"></i>
                </button>
            </div>
        </td>
        <td class="grid-elipsis" v-for="locale in locales" v-show="columnVisible(locale)" @dblclick="editField(locale)"
            :title="translatableTitle(locale)">
            <span v-if="mode !== 'edit'" v-text="translation[locale]">{{ translation[locale] }}</span>
            <input type="text"
                   class="form-control input-sm"
                   :ref="'edit-locale-' + locale"
                   :key="index + locale"
                   :name="locale"
                   :autofocus="locale === defaultLocale"
                   :value="translation[locale]"
                   v-if="inputType === 'text'"
                   v-show="mode === 'edit'"
                   @input="syncField(locale, $event.target.value)"
                   @focus="setDefaultLocale(locale)"
                   @keyup.enter="save(locale)"/>
            <textarea
                class="form-control input-sm"
                :ref="'edit-locale-' + locale"
                :key="index + locale"
                :name="locale"
                :autofocus="locale === defaultLocale"
                :value="translation[locale]"
                v-if="inputType === 'textarea'"
                v-show="mode === 'edit'"
                @input="syncField(locale, $event.target.value)"
                @focus="setDefaultLocale(locale)"
                @keyup="enterInputHandler(locale, $event)">
            </textarea>
            <div class="text-center" v-if="mode === 'delete' && translation[locale] !== ''">
                <button type="button" class="btn btn-link" @click="clear(locale)">
                    <i class="fas fa-trash text-danger"></i>
                </button>
            </div>
        </td>

        <td class="text-center">
            <div class="actions">
                <button type="button" class="btn btn-primary btn-sm" v-if="mode === 'read'" @click="toggleMode('edit')">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm" v-if="mode === 'read'" @click="toggleMode('delete')">
                    <i class="fas fa-trash"></i>
                </button>
                <button type="button" class="btn btn-success btn-sm" v-if="mode === 'edit'" @click="save">
                    <i class="fas fa-save"></i>
                </button>
                <button type="button" class="btn btn-warning btn-sm" v-if="mode !== 'read'" @click="toggleMode('read')">
                    <i class="fas fa-ban"></i>
                </button>
            </div>
        </td>
    </tr>

</template>

<script>

    import * as types from '../../store/mutation-types'

    export default {
        props: ['index', 'translationId', 'labels', 'mode', 'locales', 'defaultLocale', 'columns', 'inputType'],
        computed: {
            translation: {
                get() {
                    return this.$store.getters.getTranslationById(this.translationId);
                }
            }
        },
        updated() {
            if (!this.defaultLocale) {
                return;
            }

            if (this.mode !== 'edit') {
                return;
            }

            let fields = this.$refs['edit-locale-' + this.defaultLocale];

            if (fields.length > 0) {
                fields[0].focus();
            }
        },
        methods: {
            columnVisible(key) {
                return this.columns[key];
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
            toggleMode(mode) {
                this.$emit('toggleTranslationRow', {
                    index: this.index,
                    mode: mode
                });
            },
            editField(locale, event) {
                this.setDefaultLocale(locale);
                this.toggleMode('edit');
            },
            translatableTitle(locale, event) {
                return this.labels.doubleClickToEdit + ' translation in ' + locale;
            },
            syncField(field, value) {
                let id = this.translationId;
                this.$store.commit(types.TRANSLATION_SYNC, {id, field, value});
            },
            setDefaultLocale(locale) {
                this.$emit('setDefaultLocale', {
                    locale: locale,
                    index: this.index
                });
            },
            enterInputHandler(locale, e) {
                // handle textarea enter to submit, shift + enter for new line
                if (e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.save(locale);
                }
            },
            save(locale) {
                this.$store.dispatch('updateTranslation', {
                    translation: this.translation
                })
                    .then(_ => {
                        if (locale) {
                            this.$emit('changeTranslationRow', {
                                locale: locale,
                                index: this.index
                            });
                        }

                        this.$notify({
                            group: 'translation-grid',
                            classes: 'vue-notification success',
                            title: 'Success',
                            text: this.labels.updateSuccess
                        });
                    })
                    .catch(error => {
                        this.showError(error);
                    });
            },
            clear(locale) {
                this.$store.dispatch('deleteTranslationLocation', {
                    translation: this.translation,
                    locale: locale
                })
                    .then((id) => {

                    })
                    .catch((error) => {
                        if (401 === error.response.status) {
                            this.$notify({
                                group: 'translation-grid',
                                classes: 'vue-notification error',
                                title: 'Error',
                                text: error.response.data.message
                            });

                            return;
                        }

                        this.showError(error);
                    });
            },
            remove() {
                this.$store.dispatch('deleteTranslation', {
                    translation: this.translation
                })
                    .then((id) => {
                        this.$emit('deleteTranslationRow', {
                            index: this.index
                        });
                    })
                    .catch((error) => {
                        if (401 === error.response.status) {
                            this.$notify({
                                group: 'translation-grid',
                                classes: 'vue-notification error',
                                title: 'Error',
                                text: error.response.data.message
                            });

                            return;
                        }

                        this.showError(error);
                    });
            }
        }
    }

</script>