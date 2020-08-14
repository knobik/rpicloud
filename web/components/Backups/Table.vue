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
        <actions :backup="data.item" :node-id="nodeId" @reload="$emit('reload')" />
      </template>
      <template v-slot:cell(node.ip)="data">
        <template v-if="data.item.node">{{ data.item.node.ip }} ({{ data.item.node.hostname }})</template>
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
    nodeId: {
      type: Number,
      required: false,
      default: null
    },
    hover: {
      type: Boolean,
      default: true
    },
    fields: {
      type: Array,
      default () {
        return [
          { label: 'Filename', key: 'filename', sortable: true },
          { label: 'Node', key: 'node.ip', sortable: true },
          { label: 'Created at', key: 'created_at', sortable: true },
          { label: 'Actions', key: 'actions', thClass: ['text-right'], tdClass: ['text-right'] }
        ]
      }
    }
  }
}
</script>
