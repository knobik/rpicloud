<template>
  <b-card>
    <template v-slot:header>
      <h6 class="mb-0 d-flex justify-content-between align-items-center">
        <span>Nodes</span>

        <b-badge variant="primary" class="p-2" href="#" @click="copyData(provisionScript, 'Gist copied to clipboard.')">
          {{ provisionScript }}
        </b-badge>
      </h6>
    </template>
    <b-table
      :hover="hover"
      :striped="striped"
      :bordered="bordered"
      :small="small"
      :fixed="fixed"
      responsive="sm"
      :items="items"
      :fields="fields"
      :current-page="currentPage"
      :per-page="perPage"
    >
      <template v-slot:cell(operations)="data">
        {{ data.item.operations.length }}
      </template>
      <template v-slot:cell(ip)="data">
        <span class="clickable" @click="copyData(data.item.ip, 'IP copied to clipboard.')">{{ data.item.ip }}</span>
      </template>
      <template v-slot:cell(actions)="data">
        <actions :node="data.item" @update="$emit('update', $event)" />
      </template>
      <template v-slot:cell(netboot)="data">
        <b-badge :variant="data.item.netboot ? 'success' : 'danger'">
          {{ data.item.netboot ? 'yes' : 'no' }}
        </b-badge>
      </template>
      <template v-slot:cell(netbooted)="data">
        <b-badge
          v-if="(data.item.netboot && !data.item.netbooted) || (data.item.netbooted && !data.item.netboot)"
          variant="warning"
        >
          waiting
        </b-badge>
        <b-badge v-if="data.item.netbooted && data.item.netboot" variant="success">
          yes
        </b-badge>
        <b-badge v-if="!data.item.netbooted && !data.item.netboot" variant="danger">
          no
        </b-badge>
      </template>
      <template v-slot:cell(online)="data">
        <b-badge :variant="data.item.online ? 'success' : 'danger'">
          {{ data.item.online ? 'yes' : 'no' }}
        </b-badge>
      </template>
    </b-table>
    <nav>
      <b-pagination
        v-model="currentPage"
        :total-rows="items.length"
        :per-page="perPage"
        prev-text="Prev"
        next-text="Next"
        hide-goto-end-buttons
        align="right"
      />
    </nav>
  </b-card>
</template>

<script>
import Actions from './Actions'
import CTable from '~/components/CTable'

export default {
  components: {
    Actions
  },
  extends: CTable,
  props: {
    hover: {
      type: Boolean,
      default: true
    },
    fields: {
      type: Array,
      default () {
        return [
          { label: 'IP address', key: 'ip' },
          { label: 'Hostname', key: 'hostname' },
          { label: 'Mac', key: 'mac' },
          { label: 'Online', key: 'online' },
          { label: 'Netboot', key: 'netboot' },
          { label: 'Netbooted', key: 'netbooted' },
          { label: 'Operations in queue', key: 'operations' },
          { label: 'Actions', key: 'actions', thClass: ['text-right'], tdClass: ['text-right'] }
        ]
      }
    }
  },
  computed: {
    provisionScript () {
      return 'curl -sL ' + this.$store.state.config.url + '/api/provision/script | sudo bash -'
    }
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
    }
  }
}
</script>
