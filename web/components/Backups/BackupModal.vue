<template>
  <b-modal ref="modal" :title="`Make backup.`" @hidden="$emit('hide')" @ok="submit">
    <b-form @submit.prevent="submit" @keydown="form.errors.clear($event.target.name)">
      <b-form-group label="Node" label-for="node">
        <b-form-select
          id="node"
          v-model="form.nodeId"
          :options="simplifiedNodes"
          :class="{ 'is-invalid': form.errors.has('nodeId') }"
          @change="reloadFields"
        />
        <div v-if="form.errors.has('nodeId')" class="invalid-feedback" v-text="form.errors.get('nodeId')" />
      </b-form-group>

      <b-form-group label="Storage device to backup" label-for="storageDevices">
        <b-form-select
          id="storageDevices"
          v-model="form.storageDevice"
          :options="simplifiedStorageDevices"
          :class="{ 'is-invalid': form.errors.has('storageDevice') }"
        />
        <div v-if="form.errors.has('storageDevice')" class="invalid-feedback" v-text="form.errors.get('storageDevice')" />
      </b-form-group>
    </b-form>

    <template v-slot:modal-footer="{ ok, cancel }" :disabled="working">
      <b-button @click="cancel()">
        Cancel
      </b-button>
      <b-button variant="danger" :disabled="working" @click="ok()">
        <b-spinner v-if="working" small />
        Netboot and backup
      </b-button>
    </template>
  </b-modal>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import Form from '~/assets/js/utils/Form'

export default {
  props: {
    node: {
      type: Object,
      required: false,
      default: null
    },
    show: {
      type: Boolean,
      required: true
    }
  },
  data () {
    return {
      form: new Form({
        nodeId: null,
        hostname: null,
        storageDevice: null
      }),
      working: false,
      nodes: []
    }
  },
  computed: {
    simplifiedNodes () {
      return this.nodes.map((node) => {
        return {
          text: `${node.ip} (${node.hostname})`,
          value: node.id
        }
      })
    },
    selectedNode () {
      return this.nodes.find(node => node.id === this.form.nodeId)
    },
    simplifiedStorageDevices () {
      if (!this.selectedNode) {
        return []
      }

      return this.selectedNode.storageDevices.map((device) => {
        return {
          text: `${device.path} (${device.size}, ${this.guessStorageDeviceType(device.path)})`,
          value: device.path
        }
      })
    }
  },
  watch: {
    show (newValue) {
      if (newValue === true) {
        this.loadNodes((nodes) => {
          this.nodes = nodes
          this.form.nodeId = this.node ? this.node.id : (nodes.length ? nodes[0].id : null)
          this.reloadFields()

          this.$refs.modal.show()
        })
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

      this.form.post(`/nodes/${this.form.nodeId}/backup`)
        .then((response) => {
          this.working = false
          this.$refs.modal.hide()
          this.$emit('update', response.data.data)
        })
        .catch(() => {
          this.working = false
        })
    },
    reloadFields () {
      if (this.selectedNode && this.selectedNode.storageDevices.length > 0) {
        this.form.storageDevice = this.selectedNode.storageDevices[0].path
      } else {
        this.form.storageDevice = null
      }
    },
    guessStorageDeviceType (path) {
      if (path.includes('/dev/mmcblk0')) {
        return 'SD card'
      }
      if (path.includes('/dev/sd')) {
        return 'USB'
      }
    },
    loadNodes (callback) {
      Api.get('/nodes?online=1').then((response) => {
        if (callback) {
          callback(response.data.data)
        }
      })
    }
  }
}
</script>
