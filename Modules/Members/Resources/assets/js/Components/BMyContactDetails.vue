<template>
    <div class="b-my-contact-details">
        <b-individual-member-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'individual-member'" />
        <b-club-or-syndicate-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'club-or-syndicate'" />
        <b-affiliate-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'affiliate'" />
        <b-caag-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'caag'" />
        <b-charter-boat-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'charter-boat'" />
        <b-salmon-fishery-board-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'salmon-fishery-board'" />
        <b-consultatives-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'consultatives'" />
        <b-federation-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'federation'" />
        <b-fishery-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'fishery'" />
        <b-trade-member-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'trade-member'" />
        <b-trade-supporter-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'trade-supporter'" />
        <b-coach-my-contact-details v-if="$root.member && $root.member.membership_type_slug === 'coach'" />
    </div>
</template>

<script>
    export default {
    }
</script>
