<template>
  <div class="animated fadeIn">
    <b-row>
      <b-col lg="12">
        <NodeTable caption="Nodes" :items="items" />
      </b-col><!--/.col-->
    </b-row>
  </div>
</template>

<script>
import NodeTable from '~/components/Nodes/Table.vue'
import Api from '~/assets/js/utils/Api'

export default {
  components: {
    NodeTable
  },
  data () {
    return {
      items: []
    }
  },
  mounted () {
    this.refreshListLoop()
  },
  methods: {
    refreshListLoop () {
      Api.get('/nodes').then((response) => {
        this.items = response.data.data

        setTimeout(this.refreshListLoop, 30000)
      })
    }
  }
}
</script>
