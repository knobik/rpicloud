<template>
  <div class="animated fadeIn">
    <b-row>
      <b-col md="12">
        <operation-log v-if="runningOperation !== null" :id="runningOperation.id" :show="showLog" @hide="showLog = false" />
        <b-alert v-if="runningOperation !== null" variant="info" show>
          <div class="pointer" @click="showLog = true">
            <h5 class="alert-heading font-weight-bold d-flex align-items-center justify-content-between">
              <span class="d-flex align-items-center"><b-spinner class="mr-3" small variant="primary" />{{ runningOperation.description }}</span><small><em>{{ runningOperation.name }}</em></small>
            </h5>
            <em>{{ runningOperation.log ? runningOperation.log.split('\n').slice(-1).pop() : '' }}</em>
          </div>
        </b-alert>
      </b-col>
    </b-row>
    <b-row>
      <b-col md="12">
        <b-card>
          <div slot="header">
            <b-row>
              <b-col md="12">
                Node: <strong>{{ node.hostname }}</strong>
              </b-col>
            </b-row>
            <b-row>
              <b-col md="12">
                <small>{{ node.ip }}</small>
              </b-col>
            </b-row>
          </div>
          <b-tabs
            lazy
            card
            pills
            vertical
            nav-wrapper-class="w-25"
            class="borderless"
          >
            <b-tab active>
              <template slot="title">
                <i class="fa fa-hdd-o" /> Access
              </template>
              <access-tab :node="node" @update="overwriteNode" />
            </b-tab>
            <b-tab>
              <template slot="title">
                <i class="fa fa-database" /> Backups
              </template>
              <backup-tab :node="node" @update="overwriteNode" />
            </b-tab>
            <b-tab>
              <template slot="title">
                <i class="fa fa-tasks" /> Operations
              </template>
              <operation-tab :node="node" />
            </b-tab>
          </b-tabs>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import Api from '~/assets/js/utils/Api'
import AccessTab from '~/components/Nodes/Tabs/AccessTab'
import OperationTab from '~/components/Nodes/Tabs/OperationTab'
import BackupTab from '~/components/Nodes/Tabs/BackupTab'
import OperationLog from '~/components/Nodes/Operations/Log.vue'

export default {
  components: {
    AccessTab,
    OperationTab,
    BackupTab,
    OperationLog
  },
  asyncData ({ params }) {
    return Api.get(`/nodes/${params.id}`).then((response) => {
      return {
        node: response.data.data
      }
    })
  },
  data () {
    return {
      timer: null,
      showLog: false
    }
  },
  computed: {
    runningOperation () {
      const operation = this.node.pendingOperations.find((operation) => {
        return operation.finished_at === null
      })

      return operation || null
    }
  },
  created () {
    this.reloadNodeTimer()
  },
  beforeRouteLeave (to, from, next) {
    if (this.timer) {
      clearTimeout(this.timer)
    }

    next()
  },
  methods: {
    overwriteNode (node) {
      this.node = node
    },
    reloadNode () {
      Api.get(`/nodes/${this.node.id}`).then((response) => {
        this.node = response.data.data
      })
    },
    reloadNodeTimer () {
      if (this.timer) {
        clearTimeout(this.timer)
      }

      this.timer = setTimeout(() => {
        if (this.runningOperation !== null) {
          this.reloadNode()
        }

        this.reloadNodeTimer()
      }, 5000)
    }
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

  .pointer {
    cursor: pointer;
  }
</style>
