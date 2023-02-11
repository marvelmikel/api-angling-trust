<template>
    <div class="b-info-item">
        <label v-if="label" class="label">{{ label }}:</label>
        <span v-if="!options" class="value">{{ value }}</span>
        <span v-if="options" class="value">{{ select_value }}</span>
    </div>
</template>

<script>
    export default {
        props: ['label', 'value', 'options', 'multiple'],

        computed: {
            select_value() {
                if (this.multiple === '') {
                    return this.get_multiple_select_values();
                } else {
                    return this.get_select_value();
                }
            }
        },

        methods: {
            get_select_value() {
                if (!this.value) {
                    return null;
                }
                
                let value = this.options.find(x => x.id == this.value);

                if (!value) {
                    return null;
                }

                return value.name;
            },

            get_multiple_select_values() {
                let values = [];

                if (!this.value) {
                    return null;
                }

                for_each(this.options, (option) => {
                    if (this.value.includes(option.id)) {
                        values.push(option.name);
                    }
                });

                return values.join(', ');
            }
        }
    }
</script>
