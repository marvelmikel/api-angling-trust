<template>
    <div class="form-input" :class="{ 'has-error': has_error }" :id="name">
        <label :for="name">{{ label }} <span v-if="required === ''" class="required">*</span></label>
        <div class="fields">
            <span class="at-select">
                <select v-model="day" v-on:change="update">
                    <option></option>
                    <option value="01">1st</option>
                    <option value="02">2nd</option>
                    <option value="03">3rd</option>
                    <option value="04">4th</option>
                    <option value="05">5th</option>
                    <option value="06">6th</option>
                    <option value="07">7th</option>
                    <option value="08">8th</option>
                    <option value="09">9th</option>
                    <option value="10">10th</option>
                    <option value="11">11th</option>
                    <option value="12">12th</option>
                    <option value="13">13th</option>
                    <option value="14">14th</option>
                    <option value="15">15th</option>
                    <option value="16">16th</option>
                    <option value="17">17th</option>
                    <option value="18">18th</option>
                    <option value="19">19th</option>
                    <option value="20">20th</option>
                    <option value="21">21st</option>
                    <option value="22">22nd</option>
                    <option value="23">23rd</option>
                    <option value="24">24th</option>
                    <option value="25">25th</option>
                    <option value="26">26th</option>
                    <option value="27">27th</option>
                    <option value="28">28th</option>
                    <option v-if="this.month !== '02' || is_leap_year" value="29">29th</option>
                    <option v-if="!(['02']).includes(this.month)" value="30">30th</option>
                    <option v-if="!(['02', '04', '06', '09', '11']).includes(this.month)" value="31">31st</option>
                </select>
            </span>
            <span class="at-select">
                <select v-model="month" v-on:change="update">
                    <option></option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </span>
            <span class="at-select">
                <select v-model="year" v-on:change="update">
                    <option></option>
                    <option v-for="year in years" :value="year">{{ year }}</option>
                </select>
            </span>
        </div>
        <div class="form-errors" v-text="field.error"></div>
    </div>
</template>

<script>
    import _ from 'lodash';
    import moment from 'moment';

    export default {
        props: ['name', 'label', 'value', 'required'],

        created() {
            if (!this.field.value) {
                return;
            }

            this.day = this.field.value.day;
            this.month = this.field.value.month;
            this.year = this.field.value.year;
        },

        data() {
            return {
                years: _.range(new Date().getFullYear(), new Date().getFullYear() - 100),
                day: null,
                month: null,
                year: null
            }
        },

        computed: {
            field: {
                get() {
                    return this.value;
                },

                set(value) {
                    this.$emit('input', value);
                }
            },

            has_error() {
                return this.field.error !== null;
            },

            is_leap_year() {
                if ((parseInt(this.year) % 4) == 0) {
                    if (parseInt(this.year) % 100 == 0) {
                        if (parseInt(this.year) % 400 != 0) {
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        return true;
                    }
                } else {
                    return false;
                }
            }
        },

        watch: {
            'field.value': function() {
                if (this.field.error) {
                    this.field.error = null;
                }
            }
        },

        methods: {
            update() {
                if (!this.day || !this.month || !this.year) {
                    this.field.value = null;
                    return;
                }

                this.field.value = {
                    day: this.day,
                    month: this.month,
                    year: this.year.toString()
                };
            }
        }
    }
</script>
