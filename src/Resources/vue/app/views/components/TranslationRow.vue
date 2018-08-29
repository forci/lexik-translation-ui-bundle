<template>

    <tr>
        <td v-show="columnVisible('id')" :title="translation._id">
            <span>{{ translation._id }}</span>
        </td>
        <td v-show="columnVisible('domain')" :title="translation._domain">
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
        <td class="" v-for="locale in locales" v-show="columnVisible(locale)"
            @dblclick="editField(locale)"
            @keydown.esc="setMode('read')"
            :title="translatableTitle(locale)">
            <span v-if="mode !== 'edit' || !canEditLocale(locale) || !columnEditable(locale)" v-text="translation[locale]">{{ translation[locale] }}</span>
            <input type="text"
                   class="form-control input-sm"
                   :ref="'edit-locale-' + locale"
                   :key="index + locale"
                   :name="locale"
                   :autofocus="locale === defaultLocale"
                   :value="translation[locale]"
                   v-if="inputType === 'text' && canEditLocale(locale)"
                   v-show="mode === 'edit' && columnEditable(locale)"
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
                v-if="inputType === 'textarea' && canEditLocale(locale)"
                v-show="mode === 'edit' && columnEditable(locale)"
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
                <button type="button" class="btn btn-primary btn-sm" v-if="mode === 'read'" @click="setMode('edit')">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm" v-if="mode === 'read'" @click="setMode('delete')">
                    <i class="fas fa-trash"></i>
                </button>
                <button type="button" class="btn btn-success btn-sm" v-if="mode === 'edit'"
                        @click="save(defaultLocale)">
                    <i class="fas fa-save"></i>
                </button>
                <button type="button" class="btn btn-warning btn-sm" v-if="mode !== 'read'" @click="setMode('read')">
                    <i class="fas fa-ban"></i>
                </button>
            </div>
        </td>
    </tr>

</template>

<script>

    import * as types from '../../store/mutation-types'

    export default {
        props: ['index', 'translationId', 'labels', 'locales', 'defaultLocale', 'columns', 'editableColumns', 'inputType'],
        data() {
            return {
                editableLocales: config.editableLocales,
                mode: 'read'
            }
        },
        computed: {
            translation: {
                get() {
                    return this.$store.getters.getTranslationById(this.translationId);
                }
            }
        },
        methods: {
            focusField() {
                if (this.mode !== 'edit') {
                    return;
                }

                let fields = this.$refs['edit-locale-' + this.defaultLocale];

                if (undefined === fields) {
                    return;
                }

                if (fields.length > 0) {
                    fields[0].focus();
                }
            },
            canEditLocale(locale) {
                return this.editableLocales.includes(locale);
            },
            columnVisible(key) {
                return this.columns[key];
            },
            columnEditable(key) {
                console.log(this.editableColumns[key]);
                console.log(this.editableColumns);
                return this.editableColumns[key];
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
            setMode(mode) {
                this.mode = mode;
            },
            editField(locale) {
                this.setDefaultLocale(locale);
                this.setMode('edit');
                this.$nextTick(() => {
                    this.focusField();
                });
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
                        this.$emit('changeTranslationRow', {
                            locale: locale,
                            index: this.index
                        });

                        this.setMode('read');

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
                        //
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