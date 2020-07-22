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
      <template v-slot:cell(actions)="data">
        <actions :operation="data.item"></actions>
      </template>
      <template v-slot:cell(status)="data">
        <b-badge
          v-if="!data.item.finished_at"
          variant="warning"
        >
          working
        </b-badge>
        <b-badge v-if="data.item.finished_at && !data.item.failed" variant="success">
          success
        </b-badge>
        <b-badge v-if="!data.item.netbooted && data.item.failed" variant="danger">
          failed
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
import CTable from '~/components/CTable'
import Actions from '~/components/Nodes/Operations/Actions.vue'

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
          { label: 'Name', key: 'name', sortable: true },
          { label: 'Status', key: 'description', sortable: true },
          { label: 'Created at', key: 'created_at', sortable: true },
          { label: 'Started at', key: 'started_at', sortable: true },
          { label: 'Finished at', key: 'finished_at', sortable: true },
          { label: 'Status', key: 'status', sortable: false },
          { label: 'Action', key: 'actions', thClass: ['text-right'], tdClass: ['text-right'] }
        ]
      }
    }
  }
}
</script>
