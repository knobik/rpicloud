<template>
  <div>
    <backup-modal :node="this.node" :show="showBackupModal" @hide="showBackupModal = false" />
    <b-row class="pb-3 d-flex justify-content-between align-content-center">
      <b-col md="2" class="d-flex justify-content-start align-content-center">
        <span class="pt-1 pr-2">Show all</span>
        <c-switch
          v-model="showAll"
          color="primary"
          label
          variant="pill"
          size="lg"
          @change="loadBackups"
        />
      </b-col>
      <b-col md="2" class="text-right">
        <b-button variant="primary" @click="showBackupModal = true">
          <i class="fa fa-save" /> Make backup
        </b-button>
      </b-col>
    </b-row>
    <b-row>
      <b-col md="12">
        <backup-table :node-id="node.id" :small="true" :items="items" />
      </b-col>
    </b-row>
  </div>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import BackupTable from '~/components/Backups/Table.vue'
import BackupModal from '~/components/Backups/BackupModal.vue'
import CSwitch from '~/components/CSwitch'

export default {
  components: {
    BackupTable,
    BackupModal,
    CSwitch
  },
  props: {
    node: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      showAll: true,
      showBackupModal: false,
      items: []
    }
  },
  created () {
    this.loadBackups()
  },
  methods: {
    loadBackups () {
      Api.get('/backups' + (this.showAll ? '' : `?nodeId=${this.node.id}`)).then((response) => {
        this.items = response.data.data
      })
    }
  }
}
</script>
