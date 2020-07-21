<template>
  <b-modal
    ref="modal"
    :ok-title="params.ok"
    ok-variant="danger"
    :cancel-title="params.cancel"
    :title="params.title"
    @ok="confirm"
  >
    <p>{{ params.message }}</p>
  </b-modal>
</template>

<script>
import { events } from '~/assets/js/utils/confirm/events.js'

export default {
  data () {
    return {
      defaults: {
        title: 'Confirm',
        ok: 'Confirm',
        cancel: 'Cancel',
        message: 'Are you sure?',
        callback: () => {}
      },
      params: null
    }
  },
  created () {
    this.params = Object.assign({}, this.defaults)
  },
  mounted () {
    events.$on('open', this.open)
    events.$on('close', this.close)
  },
  methods: {
    confirm () {
      this.params.callback()
    },
    open (data) {
      this.params = Object.assign({}, this.defaults, data)
      this.$refs.modal.show()
    },
    close () {
      this.$refs.modal.hide()
    }
  }
}
</script>
