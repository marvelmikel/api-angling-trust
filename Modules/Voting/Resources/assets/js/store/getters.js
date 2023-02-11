export default {
    form: ({ form }) => form,
    has_registered: ({ has_registered }) => has_registered,
    has_submitted: ({ has_submitted }) => has_submitted,
    registration_intention: ({ registration_intention }) => registration_intention,
    can_vote: ({ form }) => !!form,
}
