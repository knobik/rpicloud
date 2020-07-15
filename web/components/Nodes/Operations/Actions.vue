<template>
  <div>
    <operation-log :id="operation.id" :show="showLog" @hide="showLog = false" />
    <b-button-group size="sm">
      <b-button variant="primary" size="sm" title="Show log" @click="showLog = true">
        <i class="fa fa-eye" />
      </b-button>
    </b-button-group>
  </div>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import OperationLog from '~/components/Nodes/Operations/Log.vue'

export default {
  components: {
    OperationLog
  },
  props: {
    operation: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
      showLog: false
    }
  },
  methods: {
  }
}
</script>
