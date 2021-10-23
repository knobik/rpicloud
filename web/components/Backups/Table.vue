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
        <actions :backup="data.item" :node-id="nodeId" @reload="$emit('reload')" @update="$emit('update', $event)" />
      </template>
      <template v-slot:cell(node.ip)="data">
        <template v-if="data.item.node">
          {{ data.item.node.ip }} ({{ data.item.node.hostname }})
        </template>
      </template>
      <template v-slot:cell(filesize)="data">
        {{ formatFilesize(data.item.filesize) }}
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
          { label: 'Size', key: 'filesize', sortable: true },
          { label: 'Created at', key: 'created_at', sortable: true },
          { label: 'Actions', key: 'actions', thClass: ['text-right'], tdClass: ['text-right'] }
        ]
      }
    }
  },
  methods: {
    formatFilesize (bytes, si = false, dp = 1) {
      const thresh = si ? 1000 : 1024

      if (Math.abs(bytes) < thresh) {
        return bytes + ' B'
      }

      const units = si
        ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
        : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB']
      let u = -1
      const r = 10 ** dp

      do {
        bytes /= thresh
        ++u
      } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1)

      return bytes.toFixed(dp) + ' ' + units[u]
    }
  }
}
</script>
