<template>
  <div class="animated fadeIn">
    <b-row>
      <b-col lg="12">
        <NodeTable :items="items" @update="updateNode" />
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
      items: [],
      timer: null
    }
  },
  mounted () {
    this.refreshListLoop()
  },
  methods: {
    updateNode (node) {
      const index = this.items.findIndex((item) => {
        return item.ip === node.ip
      })

      this.$set(this.items, index, node)
    },
    refreshListLoop () {
      if (this.timer) {
        clearInterval(this.timer)
      }

      Api.get('/nodes').then((response) => {
        this.items = response.data.data

        this.timer = setTimeout(this.refreshListLoop, 5000)
      })
    }
  }
}
</script>
