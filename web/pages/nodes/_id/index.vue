<template>
  <div class="animated fadeIn">
    <b-row>
      <b-col md="12">
        <b-card>
          <div slot="header">
            Node <em>{{ node.ip }}</em>
          </div>
          <b-tabs card pills vertical nav-wrapper-class="w-25" class="borderless">
            <b-tab active>
              <template slot="title">
                <i class="fa fa-calculator" /> Access
              </template>
              ssh and stuff
            </b-tab>
            <b-tab>
              <template slot="title">
                <i class="fa fa-tasks" /> Operations
              </template>
              <operation-tab :node="node"></operation-tab>
            </b-tab>
            <b-tab>
              <template slot="title">
                <i class="fa fa-hdd-o" /> Recovery
              </template>
              <recovery-tab :node="node"></recovery-tab>
            </b-tab>
          </b-tabs>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import RecoveryTab from '~/components/Nodes/Tabs/RecoveryTab'
import OperationTab from '~/components/Nodes/Tabs/OperationTab'

export default {
  components: {
    RecoveryTab,
    OperationTab
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

<style lang="scss">
  .borderless {
    .nav-pills.card-header {
      background-color: inherit;
    }

    .tab-content {
      border: 0;
    }
  }
</style>
