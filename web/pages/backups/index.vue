<template>
  <div class="animated fadeIn">
    <backup-modal :show="showBackupModal" @hide="showBackupModal = false"></backup-modal>
    <b-row>
      <b-col lg="12">
        <b-card>
          <template v-slot:header>
            <h6 class="mb-0 d-flex justify-content-between align-items-center">
              <span>Nodes</span>
              <b-button variant="primary" size="sm" @click="showBackupModal = true"><i class="fa fa-plus-circle"></i> Make backup</b-button>
            </h6>
          </template>
          <backup-table :items="items" @reload="reloadItems" />
        </b-card>
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
  data () {
    return {
      showBackupModal: false
    }
  },
  asyncData () {
    return Api.get('/backups').then((response) => {
      return {
        items: response.data.data
      }
    })
  },
  methods: {
    reloadItems () {
      Api.get('/backups').then((response) => {
        this.items = response.data.data
      })
    }
  }
}
</script>
