import Api from '~/assets/js/utils/Api'

export default function ({ $axios, redirect, store }) {
  Api.axios = $axios
  Api.store = store

  // debug
  // $axios.onRequest(config => {
  //   console.log('Making request to ' + config.url, config)
  // })

  $axios.onError((error) => {
    if (error.response && typeof error.response.data.error !== 'undefined') {
      if (error.response.data.error === 'AuthenticationException') {
        redirect('/auth/login')
      }
    }

    return Promise.reject(error)
  })
}
