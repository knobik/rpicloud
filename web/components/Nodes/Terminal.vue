<template>
  <div class="terminal" v-show="connected" ref="xterm"/>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import 'xterm/css/xterm.css'
import {Terminal} from 'xterm'
import {AttachAddon} from 'xterm-addon-attach'
import {FitAddon} from 'xterm-addon-fit';

export default {
  props: {
    node: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      domain: window.location.hostname,
      connected: true // fix for xterm not resizing
    }
  },
  mounted() {
    this.$xterm = new Terminal()
    this.$websocket = new WebSocket(`ws://${this.domain}:8081`)

    this.$attachAddon = new AttachAddon(this.$websocket)
    this.$xterm.loadAddon(this.$attachAddon)

    this.$fitAddon = new FitAddon();
    this.$xterm.loadAddon(this.$fitAddon);

    this.$xterm.open(this.$refs.xterm)

    this.connected = false // fix for xterm not resizing

    window.addEventListener('resize', this.onResize)
    this.onResize();

    this.requestShellAccess();
  },
  beforeDestroy() {
    this.$websocket.close()
    this.$attachAddon.dispose()
    this.$xterm.dispose()
  },
  methods: {
    onResize() {
      this.$fitAddon.fit();
    },
    requestShellAccess() {
      Api.post(`/nodes/${this.node.id}/shell-access`).then((response) => {
        this.connect(response.data.data.token)
      })
    },
    connect(token) {
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
  .terminal {
    position: absolute;
    height: 100%;
    width: 100%;
  }
</style>
