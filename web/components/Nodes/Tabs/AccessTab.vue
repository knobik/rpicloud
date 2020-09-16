<template>
  <div>
    <terminal :node="node"></terminal>
    <b-form-group label-cols="4" label-cols-lg="2" label="Netboot" label-for="input-sm">
      <c-switch
        v-model="node.netboot"
        class="m-0 mt-1"
        color="primary"
        label
        variant="pill"
        :disabled="node.pendingOperations.length > 0"
        @change="toggleNetboot"
      />
    </b-form-group>
    <b-form-group label-cols="4" label-cols-lg="2" label="Actions" label-for="input-sm">
      <b-button variant="warning" @click="reboot">
        <i class="fa fa-refresh" /> Reboot
      </b-button>
      <b-button variant="danger" @click="shutdown">
        <i class="fa fa-power-off" /> Shutdown
      </b-button>
    </b-form-group>
  </div>
</template>

<script>
import CSwitch from '~/components/CSwitch'
import Terminal from '~/components/Nodes/Terminal'
import Api from '~/assets/js/utils/Api'

export default {
  components: {
    CSwitch,
    Terminal
  },
  props: {
    node: {
      type: Object,
      required: true
    }
  },
  methods: {
    toggleNetboot (value) {
      let action = 'disable-netboot'
      if (value) {
        action = 'enable-netboot'
      }

      Api.post(`/nodes/${this.node.id}/${action}`).then((response) => {
        this.$emit('update', response.data.data)
      })
    },
    reboot () {
      this.$confirm({
        callback: () => {
          Api.post(`/nodes/${this.node.id}/reboot`, {}).then((response) => {
            this.$emit('update', response.data.data)
          })
        }
      })
    },
    shutdown () {
      this.$confirm({
        callback: () => {
          Api.post(`/nodes/${this.node.id}/shutdown`, {}).then((response) => {
            this.$emit('update', response.data.data)
          })
        }
      })
    }
  }
}
</script>
