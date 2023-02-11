<template>
    <div class="b-my-preferences">
        <template v-if="!edit_mode">
            <h3 class="at-heading is-blue">Types of Angling</h3>

            <div class="info-items">
                <b-info-item
                    :value="$root.member.meta.disciplines"
                    :options="$root.select_options.disciplines"
                    multiple
                />
            </div>

            <div class="actions">
                <button class="at-btn is-blue is-frame" @click="edit">
                    <i class="fal fa-edit"></i> Edit
                </button>
            </div>
        </template>
        <template v-if="edit_mode">
            <h3 class="at-heading is-blue">Types of Angling</h3>

            <div class="form-row">
                <b-multi-select
                    name="meta_disciplines"
                    v-model="fields.meta.disciplines"
                    :options="$root.select_options.disciplines"
                ></b-multi-select>
            </div>

            <button class="at-btn is-red is-solid" @click="edit_mode = false" :disabled="is_submitting">Cancel</button>
            <button class="at-btn is-green is-solid" @click="save" :disabled="is_submitting">Save</button>
        </template>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                is_submitting: false,
                edit_mode: false,
                fields: {
                    meta : {
                        disciplines: {
                            value: [], error: null
                        }
                    },
                }
            }
        },

        methods: {
            edit() {
                this.$fields.fill_fields(this.fields, this.$root.member);
                this.edit_mode = true;
            },

            save() {
                this.is_submitting = true;

                console.log(this.$fields.serialise_fields(this.fields));

                this.$laravel_api.post('members/me/preferences', this.$fields.serialise_fields(this.fields))
                    .then(({data}) => {
                        if (data.success) {
                            this.$root.member = data.data.member;
                            this.edit_mode = false;
                            this.$toast.success('Your contact details have been updated.');
                            this.is_submitting = false;
                        } else {
                            if (data.error.code === 422) {
                                this.$fields.fill_errors(this.fields, data.data.errors);
                                this.$toast.error("There are errors in your submission.");
                            }

                            this.is_submitting = false;
                        }
                    })
                    .catch(() => {
                        this.is_submitting = false;
                        this.$toast.error();
                    });
            }
        }
    }
</script>
