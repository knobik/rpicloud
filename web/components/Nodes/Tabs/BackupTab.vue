<template>
  <div>
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

export default {
  components: {
    BackupTable
  },
  props: {
    node: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      items: []
    }
  },
  mounted () {
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
