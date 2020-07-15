<template>
  <div class="animated fadeIn">
    <b-row>
      <b-col lg="12">
        <b-card>
          <template v-slot:header>
            <h6 class="mb-0">
              Backups
            </h6>
          </template>
          <backup-table :items="items" @reload="reloadItems" />
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import BackupTable from '~/components/Backups/Table.vue'
import Api from '~/assets/js/utils/Api'

export default {
  components: {
    BackupTable
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
