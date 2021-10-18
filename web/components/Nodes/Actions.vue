<template>
  <div>
    <b-button-group size="sm">
      <b-button variant="info" title="Details" @click="$router.push(`/nodes/${node.id}`)">
        <i class="fa fa-eye" />
      </b-button>
    </b-button-group>
    <b-button-group size="sm">
      <b-button variant="warning" title="Reboot" @click="reboot">
        <i class="fa fa-sync" />
      </b-button>
      <b-button variant="danger" title="Shutdown" @click="shutdown">
        <i class="fa fa-power-off" />
      </b-button>
    </b-button-group>
  </div>
</template>

<script>
import Api from '~/assets/js/utils/Api'

export default {
  props: {
    node: {
      type: Object,
      required: true
    }
  },
  methods: {
    reboot () {
      this.$confirm({
        message: 'Are you sure you want to reboot this node?',
        callback: () => {
          Api.post(`/nodes/${this.node.id}/reboot`, {}).then((response) => {
            this.$emit('update', response.data.data)
          })
        }
      })
    },
    shutdown () {
      this.$confirm({
        message: 'Are you sure you want to shutdown this node?',
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
