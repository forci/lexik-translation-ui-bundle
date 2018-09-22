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
                <b-btn type="button" variant="danger" size="sm" @click="remove">
                    <i class="fas fa-trash"></i>
                    Remove Translation Unit
                </b-btn>
            </div>
            <div v-if="help">
                <br/>
                <b-alert show>{{ help }}</b-alert>
            </div>
        </td>
        <td class="" v-for="locale in locales" v-show="columnVisible(locale)" :key="locale"
            @dblclick="editField(locale)"
            @keydown.esc="setMode('read')"
            :title="translatableTitle(locale)">
            <span v-if="mode !== 'edit' || !canEditLocale(locale) || !columnEditable(locale)"
                  v-text="translation[locale]">{{ translation[locale] }}</span>
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
                    @keydown.enter="enterInputHandler(locale, $event)">
            </textarea>
            <div class="text-center" v-if="mode === 'delete' && translation[locale] !== ''">
                <b-btn type="button" variant="danger" size="sm" @click="clear(locale)">
                    <i class="fas fa-trash"></i>
                    Remove Translation
                </b-btn>
            </div>
        </td>

        <td class="text-center">
            <b-btn-group size="sm">
                <b-btn type="button" variant="primary" v-if="mode === 'read'" @click="setMode('edit')">
                    <i class="fa fa-pencil-alt"></i>
                    Edit
                </b-btn>
                <b-btn type="button" variant="danger" v-if="mode === 'read'" @click="setMode('delete')">
                    <i class="fas fa-trash"></i>
                    Remove
                </b-btn>
                <b-btn type="button" variant="success" v-if="mode === 'edit'"
                       @click="save(defaultLocale)">
                    <i class="fas fa-save"></i>
                    Save
                </b-btn>
                <b-btn type="button" variant="warning" v-if="mode !== 'read'" @click="setMode('read')">
                    <i class="fas fa-ban"></i>
                    Cancel
                </b-btn>
            </b-btn-group>
        </td>
    </tr>

</template>

<script>

    import {mapGetters} from 'vuex'
    import * as types from '../store/mutation-types'

    export default {
        props: ['index', 'translationId', 'labels', 'locales', 'defaultLocale', 'columns', 'editableColumns', 'inputType'],
        data() {
            return {
                mode: 'read'
            }
        },
        computed: {
            ...mapGetters('config', {
                editableLocales: 'editableLocales',
            }),
            translation: {
                get() {
                    return this.$store.getters['translations/getTranslationById'](this.translationId);
                }
            },
            help: {
                get() {
                    return this.$store.getters['descriptions/getDescriptionByKey'](this.translation._key);
                }
            }
        },
        methods: {
            focusField(locale) {
                if (this.mode !== 'edit') {
                    return;
                }

                let localeFields = this.$refs['edit-locale-' + locale];

                if (undefined === localeFields) {
                    return;
                }

                if (localeFields.length > 0) {
                    localeFields[0].focus();
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
                    this.focusField(locale);
                });
            },
            translatableTitle(locale) {
                return this.labels.doubleClickToEdit + ' translation in ' + locale;
            },
            syncField(field, value) {
                let id = this.translationId;
                this.$store.commit('translations/' + types.TRANSLATION_SYNC, {id, field, value});
            },
            setDefaultLocale(locale) {
                this.$emit('setDefaultLocale', {
                    locale: locale,
                    index: this.index
                });
            },
            enterInputHandler(locale, e) {
                if (!e.shiftKey) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.save(locale);
                }
            },
            save(locale) {
                this.$store.dispatch('translations/update', {
                    translation: this.translation
                })
                    .then(() => {
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
                this.$store.dispatch('translations/deleteLocation', {
                    translation: this.translation,
                    locale: locale
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
                this.$store.dispatch('translations/delete', {
                    translation: this.translation
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