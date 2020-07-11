<template>
  <div>
    <b-row>
      <b-col md="12" class="pb-3 text-right">
        <button class="btn btn-primary" @click="loadOperations">
          <i class="fa fa-refresh" />
        </button>
      </b-col>
    </b-row>
    <b-row>
      <b-col md="12">
        <c-table :small="true" :hover="true" :fields="fields" :items="items" />
      </b-col>
    </b-row>
  </div>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import CTable from '~/components/CTable.vue'

export default {
  components: {
    CTable
  },
  props: {
    node: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      items: [],
      fields: [
        { label: 'Name', key: 'name' },
        { label: 'Status', key: 'description' },
        { label: 'Created at', key: 'created_at' },
        { label: 'Started at', key: 'started_at' },
        { label: 'Finished at', key: 'finished_at' }
      ]
    }
  },
  mounted () {
    this.loadOperations()
  },
  methods: {
    loadOperations () {
      Api.get(`/nodes/${this.node.id}/operations`).then((response) => {
        this.items = response.data.data
      })
    }
  }
}
</script>
