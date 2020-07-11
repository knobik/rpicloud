<template>
  <div>
    <b-button-group size="sm">
      <b-button variant="info" title="Details" @click="$router.push(`/nodes/${node.id}`)">
        <i class="fa fa-eye" />
      </b-button>
    </b-button-group>
    <b-button-group size="sm">
      <b-button variant="warning" title="Reboot" :disabled="!node.online || operationInProgress" @click="reboot">
        <i class="fa fa-refresh" />
      </b-button>
      <b-button variant="danger" title="Shutdown" :disabled="!node.online || operationInProgress" @click="shutdown">
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
  computed: {
    operationInProgress () {
      return this.node.operations.filter(function (operation) {
        return operation.finished_at === null
      }).length > 0
    }
  },
  methods: {
    reboot () {
      Api.post(`/nodes/${this.node.id}/reboot`, {}).then((response) => {
        this.$emit('update', response.data.data)
      })
    },
    shutdown () {
      Api.post(`/nodes/${this.node.id}/shutdown`, {}).then((response) => {
        this.$emit('update', response.data.data)
      })
    }
  }
}
</script>
