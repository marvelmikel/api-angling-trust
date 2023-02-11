<template>
    <form class="submit-shop-form" id="boatForm">
        <iframe :src="`/wp-json/barques/map/boat-update/form/${this.boat.ID}`"></iframe>
    </form>
</template>

<script>
export default {
    data() {
        return {
            boat: null,
        };
    },
    methods: {
        setBoat(boat) {
            this.boat = boat;
        }
    }
};
</script>
