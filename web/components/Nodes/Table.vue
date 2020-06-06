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
  components: { Actions },
  extends: CTable,
  props: {
    fields: {
      type: Array,
      default () {
        return [
          { label: 'ip address', key: 'ip' },
          { label: 'mac', key: 'mac' },
          { label: 'actions', key: 'actions', thClass: ['text-right'], tdClass: ['text-right'] }
        ]
      }
    }
  }
}
</script>
