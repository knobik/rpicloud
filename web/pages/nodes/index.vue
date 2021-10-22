<template>
  <div class="animated fadeIn">
    <b-row>
      <b-col lg="12">
        <b-card>
          <template v-slot:header>
            <h6 class="mb-0 d-flex justify-content-between align-items-center">
              <span>Nodes</span>

              <span>
                <span class="small">Add node by running:</span>
                <b-badge
                  variant="primary"
                  class="p-2"
                  href="#"
                  @click="copyData(provisionScript, 'Gist copied to clipboard.')"
                >
                  {{ provisionScript }}
                </b-badge>
              </span>
            </h6>
          </template>
          <node-table :items="items" @update="updateNode" />
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import NodeTable from '~/components/Nodes/Table.vue'
import Api from '~/assets/js/utils/Api'
import MetaWrapped from '~/assets/js/utils/MetaWrapped'

export default {
  components: {
    NodeTable
  },
  data () {
    return {
      items: [],
      defaultMeta: {
        selected: false
      },
      timer: null
    }
  },
  computed: {
    provisionScript () {
      return 'curl -sL ' + this.$store.state.config.url + '/api/provision/script | sudo bash -'
    }
  },
  created () {
    this.refreshListLoop()
  },
  beforeRouteLeave (to, from, next) {
    if (this.timer) {
      clearTimeout(this.timer)
    }

    next()
  },
  methods: {
    async copyData (data, message) {
      await this.$copyText(data)
      this.makeToast(message)
    },
    makeToast (message, variant = 'success') {
      this.$bvToast.toast(message, {
        toaster: 'b-toaster-top-right',
        variant,
        noCloseButton: true,
        solid: true,
        autoHideDelay: 2000,
        appendToast: false
      })
    },
    updateNode (node) {
      const index = this.items.findIndex((item) => {
        return item.ip === node.ip
      })

      this.$set(this.items, index, node)
    },
    refreshListLoop () {
      if (this.timer) {
        clearTimeout(this.timer)
      }

      Api.get('/nodes').then((response) => {
        this.items = MetaWrapped.wrap(response.data.data, this.items, this.defaultMeta)

        this.timer = setTimeout(this.refreshListLoop, 5000)
      })
    }
  }
}
</script>
