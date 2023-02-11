<script>

export default {
    props: {
        question: Object,
        errors: Array,
    },
    computed: {
        id() {
            return this._uid
        }
    }
}

</script>
