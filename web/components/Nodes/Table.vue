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
          { label: 'Pending operations in queue', key: 'pendingOperations' },
          { label: 'Actions', key: 'actions', thClass: ['text-right'], tdClass: ['text-right'] }
        ]
      }
    }
  },
}
</script>
