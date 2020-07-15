export default {
  store: null,
  axios: null,

  get (url, config = {}) {
    return this.axios.get(url, { ...config, ...this.config() })
  },

  post (url, data = {}, config = {}) {
    return this.request('post', url, data, config)
  },

  put (url, data = {}, config = {}) {
    return this.request('put', url, data, config)
  },

  patch (url, data = {}, config = {}) {
    return this.request('patch', url, data, config)
  },

  delete (url, config = {}) {
    return this.axios.delete(url, { ...config, ...this.config() })
  },

  request (type, url, data, config) {
    return this.axios[type.toLowerCase()](url, data, {
      ...config,
      ...this.config()
    })
  },

  config () {
    return {
      baseURL: 'http://' + window.location.hostname + ':8080/api'
    }
  }
}
