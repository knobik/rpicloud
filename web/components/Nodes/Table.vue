<template>
  <b-card :header="caption">
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
      <template v-slot:cell(actions)="data">
        <actions :node="data.item" />
      </template>
      <template v-slot:cell(netboot)="data">
        <c-switch
          v-model="data.item.netboot"
          color="primary"
          label
          variant="pill"
          size="sm"
          @change="toggleNetboot(data.item, $event)"
        />
      </template>
      <template v-slot:cell(online)="data">
        <b-badge :variant="data.item.online ? 'success' : 'danger'">{{ data.item.online ? 'yes' : 'no' }}</b-badge>
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
import Api from '~/assets/js/utils/Api'
import Actions from './Actions'
import CTable from '~/components/CTable'
import CSwitch from '~/components/CSwitch'

export default {
  components: {
    Actions,
    CSwitch
  },
  extends: CTable,
  props: {
    fields: {
      type: Array,
      default () {
        return [
          { label: 'ip address', key: 'ip' },
          { label: 'mac', key: 'mac' },
          { label: 'netboot', key: 'netboot' },
          { label: 'online', key: 'online' },
          { label: 'actions', key: 'actions', thClass: ['text-right'], tdClass: ['text-right'] }
        ]
      }
    }
  },
  methods: {
    toggleNetboot (node, value) {
      let action = 'disable-netboot'
      if (value) {
        action = 'enable-netboot'
      }

      Api.post(`/nodes/${node.id}/${action}`, {})
    }
  }
}
</script>
