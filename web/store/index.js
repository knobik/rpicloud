import createPersistedState from 'vuex-persistedstate'

export const state = () => ({
  user: {},
  authenticated: false
})

export const mutations = {
  setUser (state, user) {
    state.user = user
  },
  setAuthenticated (state, value) {
    state.authenticated = value
  }
}

export const plugins = [createPersistedState()]
