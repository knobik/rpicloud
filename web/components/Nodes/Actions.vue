<template>
  <b-button-group>
    <b-button variant="info" @click="$router.push(`/nodes/${node.id}`)">
      <i class="fa fa-eye"></i>
    </b-button>
    <b-button variant="danger">
      <i class="fa fa-trash"></i>
    </b-button>
  </b-button-group>
</template>

<script>
export default {
  props: {
    node: {
      type: Object,
      required: true
    }
  }
}
</script>
