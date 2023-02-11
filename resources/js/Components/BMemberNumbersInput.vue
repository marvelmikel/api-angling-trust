<template>
    <div class="b-member-numbers-input">
        <table>
            <thead>
            <th>Type</th>
            <th>M</th>
            <th>F</th>
            <th>Total</th>
            </thead>
            <tbody>
            <tr>
                <td>Junior (16 and Under)</td>
                <td>
                    <input v-mask="'#######'" v-model="field.junior.male.value" />
                </td>
                <td>
                    <input v-mask="'#######'" v-model="field.junior.female.value" />
                </td>
                <td v-text="member_numbers_junior_total"></td>
            </tr>
            <tr>
                <td>Senior (17-65)</td>
                <td>
                    <input v-mask="'#######'" v-model="field.senior.male.value" />
                </td>
                <td>
                    <input v-mask="'#######'" v-model="field.senior.female.value" />
                </td>
                <td v-text="member_numbers_senior_total"></td>
            </tr>
            <tr>
                <td>Veteran (65+)</td>
                <td>
                    <input v-mask="'#######'" v-model="field.veteran.male.value" />
                </td>
                <td>
                    <input v-mask="'#######'" v-model="field.veteran.female.value" />
                </td>
                <td v-text="member_numbers_veteran_total"></td>
            </tr>
            <tr>
                <td>Disabled</td>
                <td>
                    <input v-mask="'#######'" v-model="field.disabled.male.value" />
                </td>
                <td>
                    <input v-mask="'#######'" v-model="field.disabled.female.value" />
                </td>
                <td v-text="member_numbers_disabled_total"></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        props: ['name', 'label', 'value', 'required'],

        computed: {
            field: {
                get() {
                    return this.value;
                },

                set(value) {
                    this.$emit('input', value);
                }
            },

            member_numbers_junior_total() {
                return parseInt(this.field.junior.male.value) + parseInt(this.field.junior.female.value);
            },

            member_numbers_senior_total() {
                return parseInt(this.field.senior.male.value) + parseInt(this.field.senior.female.value);
            },

            member_numbers_veteran_total() {
                return parseInt(this.field.veteran.male.value) + parseInt(this.field.veteran.female.value);
            },

            member_numbers_disabled_total() {
                return parseInt(this.field.disabled.male.value) + parseInt(this.field.disabled.female.value);
            }
        }

    }
</script>
