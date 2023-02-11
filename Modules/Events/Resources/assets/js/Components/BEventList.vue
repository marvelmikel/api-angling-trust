<template>
    <div class="b-events-list">
        <div class="actions">
            <button class="at-btn is-blue has-icon" :class="{ 'is-frame': !is_mode('grid'), 'is-solid': is_mode('grid') }" @click="set_mode('grid')">
                <i class="fal fa-border-all"></i> <span>Grid View</span>
            </button>
            <button class="at-btn is-blue has-icon" :class="{ 'is-frame': !is_mode('list'), 'is-solid': is_mode('list') }" @click="set_mode('list')">
                <i class="fal fa-list-ul"></i> <span>List View</span>
            </button>
        </div>
        <span class="at-dropdown">
            <label>Sort By</label>
            <select v-model="sort_by">
                <option v-for="option in sort_options" :value="option.id">{{ option.name }}</option>
            </select>
            <svg class="svg-dropdown-arrow">
                <use xlink:href="#dropdown-arrow"></use>
            </svg>
        </span>
        <div class="at-competitions__list">
            <b-event v-for="event in sorted_events" :key="event.id" :event="event" :mode="mode" />
        </div>
        <div v-if="events.length === 0">
            <slot name="empty"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['events'],

        data() {
            return {
                mode: 'grid',
                sort_by: 'date',
                sort_options: [
                    {
                        id: 'date',
                        name: 'Date'
                    },
                    {
                        id: 'name',
                        name: 'Name'
                    }
                ]
            }
        },

        computed: {
            sorted_events() {
                let events = Object.assign([], this.events);

                if (this.sort_by === 'name') {
                    return events.sort((a, b) => {
                        return (a.name < b.name) ? -1 : (a.name > b.name) ? 1 : 0;
                    });
                }

                return events;
            }
        },

        methods: {
            is_mode(mode) {
                return this.mode === mode;
            },

            set_mode(mode) {
                this.mode = mode;
            }
        }
    }
</script>
