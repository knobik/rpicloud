<template>
  <div v-show="connected" ref="xterm" class="terminal-custom" />
</template>

<script>
import 'xterm/css/xterm.css'
import { Terminal } from 'xterm'
import { AttachAddon } from 'xterm-addon-attach'
import { FitAddon } from 'xterm-addon-fit'
import Api from '~/assets/js/utils/Api'

export default {
  props: {
    node: {
      required: true,
      type: Object
    }
  },
  data () {
    return {
      domain: window.location.hostname,
      resizeTimer: null,
      connected: true // fix for xterm not resizing
    }
  },
  mounted () {
    this.$xterm = new Terminal()
    this.$websocket = new WebSocket(`ws://${this.domain}:8081`)

    this.$attachAddon = new AttachAddon(
      this.$websocket,
      {
        bidirectional: false
      }
    )
    this.$xterm.loadAddon(this.$attachAddon)

    this.$fitAddon = new FitAddon()
    this.$xterm.loadAddon(this.$fitAddon)

    this.$xterm.open(this.$refs.xterm)
    this.connected = false // fix for xterm not resizing

    this.$xterm.onData(this.onData)

    window.addEventListener('resize', this.onResize)

    // initial resize
    setTimeout(() => {
      this.onResize()
      this.$xterm.focus()
    }, 250)

    this.requestShellAccess()
  },
  beforeDestroy () {
    this.$websocket.close()
    this.$attachAddon.dispose()
    this.$xterm.dispose()
  },
  methods: {
    onData (message) {
      const dataSend = {
        action: 'data',
        data: {
          message
        }
      }
      this.$websocket.send(JSON.stringify(dataSend))
      if (message === '0') {
        this.$xterm.write(message)
      }
    },
    onResize () {
      // debounce
      clearTimeout(this.resizeTimer)
      this.resizeTimer = setTimeout(() => {
        this.$fitAddon.fit()

        if (this.connected) {
          this.$websocket.send(JSON.stringify({
            action: 'resize',
            data: {
              columns: 10,
              rows: 10
            }
          }))
        }
      }, 250)
    },
    requestShellAccess () {
      Api.post(`/nodes/${this.node.id}/shell-access`).then((response) => {
        this.connect(response.data.data.token)
      })
    },
    connect (token) {
      this.$websocket.send(JSON.stringify({
        action: 'auth',
        data: {
          token
        }
      }))

      this.connected = true
    }
  }
}
</script>
<style type="scss">
.terminal-custom {
  height: 100%;
  width: auto;
}
</style>
