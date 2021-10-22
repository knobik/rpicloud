<template>
  <b-modal ref="modal" :title="`Change boot order for ${node.hostname}`" size="lg" @hidden="$emit('hide')" @ok="submit">
    <b-form @submit.prevent="submit" @keydown="form.errors.clear($event.target.name)">
      <b-alert variant="danger" show>
        <i class="fa fa-exclamation-triangle" /> Be very carefull of your boot order, invalid or empty boot order can brick your raspberry pi!
      </b-alert>
      <p>
        Generated boot order: <code>{{ generatedOrder }}</code> <a target="_blank" href="https://www.raspberrypi.com/documentation/computers/raspberry-pi.html#BOOT_ORDER"><i class="fa fa-question-circle" /></a>
      </p>
      <div v-if="form.errors.has('bootOrder')" class="invalid-feedback" v-text="form.errors.get('bootOrder')" />
      <c-boot-order v-model="form.bootOrder" />
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

export default {
  components: {
    CBootOrder
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
      form: new Form({
        bootOrder: this.node.bootOrder
      }),
      working: false
    }
  },
  computed: {
    generatedOrder () {
      // clone the array, leave original unchanged
      return '0x' + [...this.form.bootOrder].reverse().map(({ id }) => id).join('')
    }
  },
  watch: {
    show (newValue) {
      if (newValue === true) {
        if (this.force.length > 0) {
          this.form.bootOrder = this.force
        } else {
          this.form.bootOrder = this.node.bootOrder
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

      // this.form.post(`/nodes/${this.form.nodeId}/update-boot-order`)
      //   .then((response) => {
      //     this.working = false
      //     this.$refs.modal.hide()
      //     this.$emit('update', response.data.data)
      //   })
      //   .catch(() => {
      //     this.working = false
      //   })
    }
  }
}
</script>
