export default {
    async getForm({ commit }) {
        const { data: {form, has_registered, registration_intention} } = await this.$laravel_api.get('/api/voting/forms/mine');
        commit('setForm', form)
        commit('setHasRegistered', has_registered)
        commit('setRegistrationIntention', registration_intention)
        commit('setHasSubmitted', form.has_submitted)
    },

    async saveRegistration({ commit }, acceptance) {
        await this.$laravel_api.post('/api/voting/forms/mine/registration', acceptance);
        commit('setHasRegistered', true)
    },

    async saveResponses({ commit }, responses) {
        await this.$laravel_api.post('/api/voting/forms/mine/responses', responses);
        commit('setHasSubmitted', true)
    },
}
