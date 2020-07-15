<template>
  <b-modal
    ref="modal"
    :title="`Operation #${id}`"
    size="xl"
    ok-only
    @hide="$emit('hide')"
  >
    <div v-if="operation">
      <b-textarea :value="operation.log" disabled rows="20" />
    </div>
  </b-modal>
</template>

<script>
import Api from '~/assets/js/utils/Api'

export default {
  props: {
    id: {
      type: Number,
      required: true
    },
    show: {
      type: Boolean,
      required: true
    }
  },
  data () {
    return {
      operation: null,
      live: true,
      timer: null
    }
  },
  watch: {
    show (newValue) {
      if (newValue === true) {
        this.loadOperation()
        this.$refs.modal.show()
      }
    }
  },
  mounted () {
    this.reloadTimer()
  },
  methods: {
    loadOperation () {
      Api.get(`/operations/${this.id}`).then((response) => {
        this.operation = response.data.data
      })
    },
    reloadTimer () {
      if (this.timer) {
        clearTimeout(this.timer)
      }

      this.timer = setTimeout(() => {
        if (this.live && this.show) {
          this.loadOperation()
        }

        this.reloadTimer()
      }, 5000)
    }
  }
}
</script>
