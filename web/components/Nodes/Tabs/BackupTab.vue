<template>
  <div>
    <b-row>
      <b-col md="12" class="pb-3 text-right">
        <backup-modal :node="this.node" :show="showBackupModal" @hide="showBackupModal = false"></backup-modal>
        <b-button variant="primary" @click="showBackupModal = true"><i class="fa fa-save"></i> Make backup</b-button>
      </b-col>
    </b-row>
    <b-row>
      <b-col md="12">
        <backup-table :small="true" :items="items" />
      </b-col>
    </b-row>
  </div>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import BackupTable from '~/components/Backups/Table.vue'
import BackupModal from '~/components/Backups/BackupModal.vue'

export default {
  components: {
    BackupTable,
    BackupModal
  },
  props: {
    node: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      showBackupModal: false,
      items: []
    }
  },
  created () {
    this.loadBackups()
  },
  methods: {
    loadBackups () {
      Api.get(`/backups?nodeId=${this.node.id}`).then((response) => {
        this.items = response.data.data
      })
    }
  }
}
</script>
