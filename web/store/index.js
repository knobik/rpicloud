import createPersistedState from 'vuex-persistedstate'

export const state = () => ({
  user: {},
  authenticated: false,
  config: null
})

export const mutations = {
  setUser (state, user) {
    state.user = user
  },
  setAuthenticated (state, value) {
    state.authenticated = value
  },
  setConfig (state, config) {
    state.config = config
  }
}

export const plugins = [createPersistedState()]
