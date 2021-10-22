<template>
  <terminal :node="node" />
</template>

<script>
import Api from '~/assets/js/utils/Api'
import Terminal from '~/components/Nodes/Terminal'

export default {
  layout: 'terminal',
  components: {
    Terminal
  },
  asyncData ({ params }) {
    return Api.get(`/nodes/${params.id}`).then((response) => {
      return {
        node: response.data.data
      }
    })
  }
}
</script>
