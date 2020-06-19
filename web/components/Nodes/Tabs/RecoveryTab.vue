<template>
  <div>
    <b-form-group label-cols="4" label-cols-lg="2" label="Netboot" label-for="input-sm">
      <c-switch
        v-model="node.netboot"
        class="m-0 mt-1"
        color="primary"
        label
        variant="pill"
        @change="toggleNetboot"
      />
    </b-form-group>
  </div>
</template>

<script>
// import { Switch as cSwitch } from '@coreui/vue'
import CSwitch from '~/components/CSwitch'
import Api from '~/assets/js/utils/Api'

export default {
  components: {
    CSwitch
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

      Api.post(`/nodes/${this.node.id}/${action}`, {})
    }
  }
}
</script>
