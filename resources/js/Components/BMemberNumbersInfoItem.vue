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
                <td v-text="member.meta.member_numbers.junior.male"></td>
                <td v-text="member.meta.member_numbers.junior.female"></td>
                <td v-text="member_numbers_junior_total"></td>
            </tr>
            <tr>
                <td>Senior (17-65)</td>
                <td v-text="member.meta.member_numbers.senior.male"></td>
                <td v-text="member.meta.member_numbers.senior.female"></td>
                <td v-text="member_numbers_senior_total"></td>
            </tr>
            <tr>
                <td>Veteran (65+)</td>
                <td v-text="member.meta.member_numbers.veteran.male"></td>
                <td v-text="member.meta.member_numbers.veteran.female"></td>
                <td v-text="member_numbers_veteran_total"></td>
            </tr>
            <tr>
                <td>Disabled</td>
                <td v-text="member.meta.member_numbers.disabled.male"></td>
                <td v-text="member.meta.member_numbers.disabled.female"></td>
                <td v-text="member_numbers_disabled_total"></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        props: ['member'],

        computed: {
            member_numbers_junior_total() {
                return parseInt(this.member.meta.member_numbers.junior.male) + parseInt(this.member.meta.member_numbers.junior.female);
            },

            member_numbers_senior_total() {
                return parseInt(this.member.meta.member_numbers.senior.male) + parseInt(this.member.meta.member_numbers.senior.female);
            },

            member_numbers_veteran_total() {
                return parseInt(this.member.meta.member_numbers.veteran.male) + parseInt(this.member.meta.member_numbers.veteran.female);
            },

            member_numbers_disabled_total() {
                return parseInt(this.member.meta.member_numbers.disabled.male) + parseInt(this.member.meta.member_numbers.disabled.female);
            }
        }

    }
</script>
