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
        <operation-table :small="true" :items="items" />
      </b-col>
    </b-row>
  </div>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import OperationTable from '~/components/Nodes/Operations/Table.vue'

export default {
  components: {
    OperationTable
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
