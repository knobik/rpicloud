<template>
  <div class="animated fadeIn">
    <backup-modal :show="showBackupModal" @hide="showBackupModal = false"></backup-modal>
    <upload-modal :show="showUploadModal" @hide="showUploadModal = false" @update="reloadItems"></upload-modal>
    <b-row>
      <b-col lg="12">
        <b-card>
          <template v-slot:header>
            <h6 class="mb-0 d-flex justify-content-between align-items-center">
              <span>Nodes</span>
              <div>
                <b-button variant="primary" size="sm" @click="showUploadModal = true"><i class="fa fa-upload"></i> Upload .img</b-button>
                <b-button variant="primary" size="sm" @click="showBackupModal = true"><i class="fa fa-save"></i> Make backup</b-button>
              </div>
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
import UploadModal from '~/components/Backups/UploadModal.vue'

export default {
  components: {
    BackupTable,
    BackupModal,
    UploadModal
  },
  data () {
    return {
      showBackupModal: false,
      showUploadModal: false
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
