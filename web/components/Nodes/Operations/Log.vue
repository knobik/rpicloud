<template>
  <b-modal
    ref="modal"
    :title="`Operation #${id}`"
    size="xl"
    ok-only
    @hide="$emit('hide')"
  >
    <div v-if="operation">
      <b-textarea :id="`operation-log-${operation.id}`" :value="operation.log" disabled rows="20" />
      <div class="d-flex justify-content-start align-items-center w-auto mt-2">
        <c-switch
          v-model="followLog"
          class="m-0 mt-1"
          color="primary"
          variant="pill"
          @change="toggleFollowLog"
        />
        <span class="ml-2">
          Follow the log
        </span>
      </div>
    </div>
  </b-modal>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import CSwitch from '~/components/CSwitch'

export default {
  components: {
    CSwitch
  },
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
      timer: null,
      followLog: true
    }
  },
  watch: {
    show (newValue) {
      if (newValue === true) {
        this.loadOperation()
        this.$refs.modal.show()
      } else if (this.timer) {
        this.$refs.modal.hide()
        clearTimeout(this.timer)
      }
    }
  },
  created () {
    this.reloadTimer()
  },
  beforeRouteLeave (to, from, next) {
    if (this.timer) {
      clearTimeout(this.timer)
    }

    next()
  },
  methods: {
    toggleFollowLog (value) {
      this.followLog = value
      this.scrollLog()
    },
    loadOperation () {
      Api.get(`/operations/${this.id}`).then((response) => {
        this.operation = response.data.data
        if (this.followLog) {
          setTimeout(() => {
            this.scrollLog()
          }, 100)
        }
      })
    },
    scrollLog () {
      const logTa = document.getElementById(`operation-log-${this.operation.id}`)
      logTa.scrollTop = logTa.scrollHeight
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
