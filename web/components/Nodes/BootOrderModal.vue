<template>
  <b-modal ref="modal" :title="`Change boot order for ${node.hostname}`" size="lg" @hidden="$emit('hide')" @ok="submit">
    <b-form @submit.prevent="submit" @keydown="errors.clear($event.target.name)">
      <b-alert variant="danger" show>
        <i class="fa fa-exclamation-triangle" /> Be very carefull of your boot order, invalid or empty boot order can brick your raspberry pi! If that happened to you, then you need to use the <a target="_blank" href="https://www.raspberrypi.com/documentation/computers/raspberry-pi.html#updating-the-bootloader">Raspberry Pi Imager software to reset the bootloader.</a>
      </b-alert>
      <p>
        Selected boot order: <code>{{ generatedOrder }}</code> <a target="_blank" href="https://www.raspberrypi.com/documentation/computers/raspberry-pi.html#BOOT_ORDER"><i class="fa fa-question-circle" /></a>
      </p>
      <c-boot-order v-model="bootOrder" />
      <div class="d-flex justify-content-start align-items-center w-auto mt-5">
        <c-switch
          v-model="includeDhcpOption"
          class="m-0 mt-1"
          color="primary"
          variant="pill"
        />
        <span class="ml-2">
          Include <code>DHCP_TIMEOUT=5000</code> and <code>DHCP_REQ_TIMEOUT=500</code> for faster netboot. (recommended)
        </span>
      </div>
    </b-form>

    <template v-slot:modal-footer="{ ok, cancel }" :disabled="working">
      <b-button @click="cancel()">
        Cancel
      </b-button>
      <b-button variant="danger" :disabled="working" @click="ok()">
        <b-spinner v-if="working" small />
        <i class="fa fa-exclamation-triangle" /> Change boot order
      </b-button>
    </template>
  </b-modal>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import Form from '~/assets/js/utils/Form'
import CBootOrder from '~/components/CBootOrder.vue'
import CSwitch from '~/components/CSwitch'

export default {
  components: {
    CBootOrder,
    CSwitch
  },
  props: {
    node: {
      type: Object,
      required: false,
      default: null
    },
    show: {
      type: Boolean,
      required: true
    },
    force: {
      type: Array,
      default () {
        return []
      }
    }
  },
  data () {
    return {
      includeDhcpOption: true,
      bootOrder: this.node.bootOrder,
      working: false
    }
  },
  computed: {
    generatedOrder () {
      // clone the array, leave original unchanged
      return '0x' + [...this.bootOrder].reverse().map(({ id }) => id).join('')
    }
  },
  watch: {
    show (newValue) {
      if (newValue === true) {
        if (this.force.length > 0) {
          this.bootOrder = this.force
        } else {
          this.bootOrder = this.node.bootOrder
        }
        this.$refs.modal.show()
      } else {
        this.working = false
        this.$refs.modal.hide()
      }
    }
  },
  methods: {
    submit (e) {
      e.preventDefault()
      this.working = true
      Api.post(`/nodes/${this.node.id}/update-boot-order`, {
        includeDhcpOption: this.includeDhcpOption,
        bootOrder: this.bootOrder
      })
        .then((response) => {
          this.working = false
          this.$refs.modal.hide()
          this.$emit('update', response.data.data)
        })
        .catch(() => {
          this.working = false
        })
    }
  }
}
</script>
