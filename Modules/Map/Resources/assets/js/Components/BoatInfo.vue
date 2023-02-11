<template>
    <b-map-modal
        v-if="show"
        :header="true"
        :title="boat.name"
        v-on:close="close"
        class="is-midnight"
    >
        <b-tabs id="fishing-location-tabs">
            <template v-slot:nav>
                <b-tabs-nav-item tab="boat" is-active>
                    Boat
                </b-tabs-nav-item>
                <b-tabs-nav-item tab="trip">
                    Trip
                </b-tabs-nav-item>
                <b-tabs-nav-item tab="prices">
                    Prices
                </b-tabs-nav-item>
                <b-tabs-nav-item tab="contact">
                    Contact
                </b-tabs-nav-item>
                <b-tabs-nav-item tab="images" v-if="images.length">
                    Images
                </b-tabs-nav-item>
            </template>

            <template v-slot:tabs>
                <b-tabs-tab tab="boat" is-active>
                    <p v-if="boat_information.skipper"><strong>Skipper:</strong> {{ boat_information.skipper }}</p>
                    <p v-if="boat_information.boat_type"><strong>Boat Type:</strong> {{ boat_information.boat_type }}</p>
                    <p v-if="boat_information.power"><strong>Power:</strong> {{ boat_information.power }}</p>
                    <p v-if="boat_information.persons"><strong>Persons:</strong> {{ boat_information.persons }}</p>
                    <div v-if="boat_information.facilities.length">
                        <p><strong>Facilities:</strong></p>
                        <ul>
                            <li v-for="facility in boat_information.facilities">{{ facility }}</li>
                        </ul>
                    </div>
                </b-tabs-tab>

                <b-tabs-tab tab="trip" is-active>
                    <p v-if="trip_information.hours"><strong>Hours:</strong> {{ trip_information.hours }}</p>
                    <p><strong>Tackle Available:</strong> {{ trip_information.tackle_available ? 'Yes' : 'No' }}</p>
                    <p><strong>Bait Available:</strong> {{ trip_information.bait_available ? 'Yes' : 'No' }}</p>
                    <div v-if="trip_information.trip_types.length">
                        <p><strong>Trip Types:</strong></p>
                        <ul>
                            <li v-for="tripType in trip_information.trip_types">{{ tripType }}</li>
                        </ul>
                    </div>
                </b-tabs-tab>

                <b-tabs-tab tab="prices" is-active>
                    <p v-if="prices.individuals"><strong>Individuals:</strong> {{ prices.individuals }}</p>
                    <p v-if="prices.whole_boat_inshore"><strong>Whole Boat (inshore):</strong> {{ prices.whole_boat_inshore }}</p>
                    <p v-if="prices.whole_boat_wreck_deep_sea"><strong>Whole Boat (wreck / deep sea):</strong> {{ prices.whole_boat_wreck_deep_sea }}</p>
                    <p v-if="prices.other"><strong>Other:</strong> {{ prices.other }}</p>
                </b-tabs-tab>

                <b-tabs-tab tab="contact" is-active>
                    <p v-if="contact.website"><strong>Website:</strong> <span style="text-decoration: underline;" v-html="formatWebsite(contact.website)"></span></p>
                    <p v-if="contact.email"><strong>Email:</strong> <span style="text-decoration: underline;" v-html="formatEmail(contact.email)"></span></p>
                    <p v-if="contact.telephone"><strong>Telephone:</strong> <span style="text-decoration: underline;" v-html="formatPhone(contact.telephone)"></span></p>
                    <p v-if="contact.booking_info" style="white-space: pre-line;"><strong>Booking info:</strong> {{ contact.booking_info }}</p>
                </b-tabs-tab>

                <b-tabs-tab tab="images" is-active>
                    <div class="images__lightbox">
                        <a :href="image.url"
                           style="line-height: 0; display: inline-block; margin-right: 7px; margin-bottom: 5px;"
                           v-for="{ image } in images"
                        >
                            <img :src="image.sizes.thumbnail" :alt="image.alt" />
                        </a>
                    </div>
                </b-tabs-tab>

            </template>
        </b-tabs>
    </b-map-modal>
</template>

<script>
import lightGallery from 'lightgallery'
import 'lightgallery/scss/lightgallery.scss'
import 'lightgallery/scss/lg-thumbnail.scss'
import lgThumbnail from 'lightgallery/plugins/thumbnail'

export default {
    updated() {
        const lightbox = document.querySelector('.images__lightbox');
        if (lightbox) {
            lightGallery(lightbox, {
                plugins: [lgThumbnail],
                controls: false,
            })
        }
    },
    data() {
        return {
            show: false,
            boat: null
        };
    },
    computed: {
        boat_information: function() {
            return this.boat ? this.boat.boat_information : {};
        },
        trip_information: function() {
            return this.boat ? this.boat.trip_information : {};
        },
        prices: function() {
            return this.boat ? this.boat.prices : {};
        },
        contact: function() {
            return this.boat ? this.boat.contact_information : {};
        },
        images: function() {
            return this.boat ? this.boat.images : [];
        }
    },
    methods: {
        open(boat) {
            this.boat = boat;
            this.show = true;
        },
        close() {
            this.show = false;
            this.boat = null;
        },
        formatWebsite(website) {
            const regExp = new RegExp(/^https?:\/\//);
            const label = website.replace(regExp, '');
            let href = website;
            if (!regExp.test(href)) {
                href = `https://${href}`;
            }

            return `<a href="${href}" target="_blank" rel="nofollow noreferrer">${label}</a>`;
        },
        formatEmail(email) {
            return `<a href="mailto:${email}">${email}</a>`;
        },
        formatPhone(telephone) {
            let href = telephone.replace(/[^\d+]/, '');

            if (!href.match(/^(\+|00)/)) {
                href = href.replace(/^0/, '+44');
            }

            return `<a href="tel:${href}">${telephone}</a>`;
        }
    }
};
</script>
