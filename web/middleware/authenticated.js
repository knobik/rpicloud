import Api from '~/assets/js/utils/Api'

export default function ({ store, redirect }) {
  if (!store.state.authenticated) {
    return redirect('/auth/login')
  }

  Api.store = store
}
