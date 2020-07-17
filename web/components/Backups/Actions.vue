<template>
  <div>
    <restore-modal :backup="backup" :node-id="nodeId" :show="showRestoreModal" @hide="showRestoreModal = false" />
    <b-button-group size="sm">
      <b-button variant="primary" title="Restore" @click="restore">
        <i class="fa fa-refresh" />
      </b-button>
      <b-button variant="danger" title="Delete" @click="remove">
        <i class="fa fa-trash" />
      </b-button>
    </b-button-group>
  </div>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import RestoreModal from '~/components/Backups/RestoreModal.vue'

export default {
  components: {
    RestoreModal
  },
  props: {
    backup: {
      type: Object,
      required: true
    },
    nodeId: {
      type: Number,
      required: false,
      default: null
    }
  },
  data () {
    return {
      showRestoreModal: false
    }
  },
  methods: {
    restore () {
      this.showRestoreModal = true
    },
    remove () {
      this.$confirm({
        callback: () => {
          Api.delete(`/backups/${this.backup.id}`).then(() => {
            this.$emit('reload')
          })
        }
      })
    }
  }
}
</script>
