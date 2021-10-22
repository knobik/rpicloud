<template>
  <div>
    <boot-order-modal :node="node" :show="showBootOrderModal" @hide="showBootOrderModal = false" />

    <b-form-group label-cols="4" label-cols-lg="2" label="SSH access" label-for="input-sm">
      <b-button variant="primary" @click="openConsole">
        <i class="fa fa-terminal" /> Open console
      </b-button>
    </b-form-group>
    <b-form-group label-cols="4" label-cols-lg="2" label="Netboot" label-for="input-sm">
      <c-switch
        v-model="node.netboot"
        class="m-0 mt-1"
        color="primary"
        variant="pill"
        :disabled="node.pendingOperations.length > 0"
        @change="toggleNetboot"
      />
    </b-form-group>
    <b-form-group label-cols="4" label-cols-lg="2" label="Actions" label-for="input-sm">
      <b-button-group>
        <b-button variant="warning" @click="reboot">
          <i class="fa fa-sync" /> Reboot
        </b-button>
        <b-button variant="danger" @click="shutdown">
          <i class="fa fa-power-off" /> Shutdown
        </b-button>
      </b-button-group>

      <!-- visible only for RPI4 -->
      <template v-if="node.version >= 4">
        <b-popover
          v-if="!canChangeBootOrder"
          :target="`bootorder-modal-button-${node.id}`"
          title="Boot issue detected."
          triggers="hover"
          placement="top"
          variant="danger"
        >
          <template #title>
            Bootloader too old!
          </template>
          <p>
            System detected an old bootloader. Please update your raspberry pi bootloader to latest version.
          </p>
          <p>
            <a href="https://www.raspberrypi.com/documentation/computers/raspberry-pi.html#editing-the-configuration" target="_blank" class="text-dark"><i class="fa fa-question-circle" /> More information can be found here.</a>
          </p>
        </b-popover>
        <b-button :id="`bootorder-modal-button-${node.id}`" variant="danger" :disabled="!canChangeBootOrder" @click="showBootOrderModal = true">
          <i class="fa fa-align-justify" /> Change boot order
        </b-button>
      </template>
    </b-form-group>
  </div>
</template>

<script>
import CSwitch from '~/components/CSwitch'
import Api from '~/assets/js/utils/Api'
import BootOrderModal from '~/components/Nodes/BootOrderModal.vue'

export default {
  components: {
    CSwitch,
    BootOrderModal
  },
  props: {
    node: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      showBootOrderModal: false
    }
  },
  computed: {
    canChangeBootOrder () {
      return this.node.bootloaderTimestamp > this.$store.state.config.requiredBootloaderTimestamp
    }
  },
  methods: {
    openConsole () {
      window.open(`/nodes/${this.node.id}/terminal`, `SSH for ${this.node.id}`, 'width=1024,height=768')
    },
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
