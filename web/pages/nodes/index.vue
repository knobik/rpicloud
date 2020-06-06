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
  asyncData ({ params }) {
    return Api.get('/nodes').then((response) => {
      return {
        items: response.data.data
      }
    })
  },
  data () {
    return {
      items: []
    }
  }
}
</script>
