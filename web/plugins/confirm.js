import Vue from 'vue'
import { events } from '~/assets/js/utils/confirm/events.js'

const confirm = (params) => {
  if (typeof params !== 'object' || Array.isArray(params)) {
    let caughtType = typeof params
    if (Array.isArray(params)) { caughtType = 'array' }

    throw new Error(
      `Options type must be an object. Caught: ${caughtType}. Expected: object`
    )
  }

  if (typeof params === 'object') {
    if (
      // eslint-disable-next-line no-prototype-builtins
      params.hasOwnProperty('callback') &&
      typeof params.callback !== 'function'
    ) {
      const callbackType = typeof params.callback
      throw new Error(
        `Callback type must be an function. Caught: ${callbackType}. Expected: function`
      )
    }
    events.$emit('open', params)
  }
}
confirm.close = () => {
  events.$emit('close')
}

Vue.prototype.$confirm = confirm
Vue.$confirm = confirm
