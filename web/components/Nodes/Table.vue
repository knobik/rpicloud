<template>
  <div>
    <b-table
      show-empty
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
      <template v-slot:head(selected)="data">
        <b-dropdown size="sm" split variant="light">
          <template v-slot:button-content>
            <b-checkbox @change="toggleAll" />
          </template>
          <b-dropdown-item @click="reboot">
            Reboot
          </b-dropdown-item>
          <b-dropdown-item @click="shutdown">
            Shutdown
          </b-dropdown-item>
        </b-dropdown>
      </template>
      <template v-slot:cell(selected)="data">
        <b-checkbox v-model="data.item.selected" class="ml-2" />
      </template>

      <template v-slot:cell(pendingOperations)="data">
        {{ data.item.pendingOperations.length }}
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
        align="right"
      />
    </nav>
  </div>
</template>

<script>
import Actions from './Actions'
import CTable from '~/components/CTable'
import Api from '~/assets/js/utils/Api'

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
          { label: '', key: 'selected' },
          { label: 'IP address', key: 'ip', sortable: true },
          { label: 'Hostname', key: 'hostname', sortable: true },
          { label: 'Mac', key: 'mac', sortable: true },
          { label: 'Online', key: 'online', sortable: true },
          { label: 'Netboot', key: 'netboot', sortable: true },
          { label: 'Netbooted', key: 'netbooted', sortable: true },
          { label: 'Pending operations in queue', key: 'pendingOperations', sortable: true },
          { label: 'Actions', key: 'actions', thClass: ['text-right'], tdClass: ['text-right'] }
        ]
      }
    }
  },
  computed: {
    selectedItems () {
      return this.items.filter(item => item.selected)
    },
    selectedItemsIds () {
      return this.selectedItems.map(item => item.id)
    }
  },
  methods: {
    reboot () {
      Api.post('/nodes/bulk-reboot', { nodeId: this.selectedItemsIds })
    },
    shutdown () {
      Api.post('/nodes/bulk-shutdown', { nodeId: this.selectedItemsIds })
    },
    toggleAll (value) {
      this.items.forEach((item) => {
        item.selected = value
      })
    }
  }
}
</script>
