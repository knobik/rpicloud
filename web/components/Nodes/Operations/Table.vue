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
          { label: 'Name', key: 'name' },
          { label: 'Status', key: 'description' },
          { label: 'Created at', key: 'created_at' },
          { label: 'Started at', key: 'started_at' },
          { label: 'Finished at', key: 'finished_at' },
          { label: 'Action', key: 'actions', thClass: ['text-right'], tdClass: ['text-right'] }
        ]
      }
    }
  }
}
</script>
