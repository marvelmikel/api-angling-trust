<template>
    <div class="b-my-preferences">
        <b-individual-member-my-preferences v-if="$root.member && $root.member.membership_type_slug === 'individual-member'" />
        <b-club-or-syndicate-my-preferences v-if="$root.member && $root.member.membership_type_slug === 'club-or-syndicate'" />
        <b-fishery-my-preferences v-if="$root.member && $root.member.membership_type_slug === 'fishery'" />
        <b-trade-member-my-preferences v-if="$root.member && $root.member.membership_type_slug === 'trade-member'" />
        <b-trade-supporter-my-preferences v-if="$root.member && $root.member.membership_type_slug === 'trade-supporter'" />
    </div>
</template>

<script>
    export default {
    }
</script>
