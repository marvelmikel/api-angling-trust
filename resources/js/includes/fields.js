
export default
{
    serialise_fields(fields) {
        let data = {};

        for_each(fields, (key, field) => {
            if ('value' in field) {
                data[key] = field.value;
            } else {
                data[key] = this.serialise_fields(field);
            }
        });

        return data;
    },

    clear_errors(fields) {
        for_each(fields, (key, field) => {
            if ('value' in field) {
                fields[key].error = null;
            } else {
                this.clear_errors(fields[key]);
            }
        });
    },

    fill_errors(fields, errors) {
        this.clear_errors(fields);

        for_each(errors, (key, value) => {
            let field = fields;

            key.split('.').forEach((key) => {
                field = field[key];
            });

            field.error = value[0];
        });

        let first = '#' + Object.keys(errors)[0];
        first = first.split('.').join('_');

        console.log(first);

        VueScrollTo.scrollTo(first, 500, {
            offset: -100
        });
    },

    fill_fields(fields, data) {
        for_each(fields, (key, field) => {
            if ('value' in field) {
                fields[key].value = data[key];
            } else {
                this.fill_fields(fields[key], data[key]);
            }
        });
    },

    initialise_fields(fields) {
        let data = {};

        for_each(fields, (field) => {
            this.initialise_field(data, field);
        });

        return data;
    },

    initialise_field(data, field) {
        if (!field.includes('.')) {
            data[field] = {
                value: null, error: null
            };
        } else {
            field = field.split('.');
            let key = Object.assign(field[0]);

            if (typeof data[key] === 'undefined') {
                data[key] = {};
            }

            field.shift();
            field = field.join('.');

            this.initialise_field(data[key], field);
        }
    }
}
