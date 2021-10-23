<template>
  <div>
    <h5>Boot order</h5>
    <b-list-group class="mb-3">
      <b-list-group-item v-if="orderedItems.length === 0" variant="danger" class="d-flex justify-content-center align-items-center">
        No boot order selected. Drag and drop items here.
      </b-list-group-item>
      <draggable
        v-model="orderedItems"
        group="order"
        ghost-class="list-group-item-light"
        @end="$emit('input', orderedItems)"
      >
        <b-list-group-item v-for="(item, index) in orderedItems" :key="item.id" :class="orderedItems.length > 1 ? 'drag' : ''" class="d-flex justify-content-between align-items-center">
          <span><b-badge variant="primary" class="mr-2">{{ index + 1 }}</b-badge> {{ item.name }}</span> <i v-if="orderedItems.length > 1" class="fa fa-align-justify" />
        </b-list-group-item>
      </draggable>
    </b-list-group>
    <h5>Not used</h5>
    <b-list-group>
      <draggable
        v-model="leftOverItems"
        ghost-class="list-group-item-primary"
        group="order"
        @end="$emit('input', orderedItems)"
      >
        <b-list-group-item v-for="(item, index) in leftOverItems" :key="item.id" variant="light" class="drag d-flex justify-content-between align-items-center">
          <span><b-badge variant="primary" class="mr-2">{{ index + orderedItems.length + 1 }}</b-badge> {{ item.name }}</span> <i class="fa fa-align-justify" />
        </b-list-group-item>
      </draggable>
    </b-list-group>
  </div>
</template>

<script>
import draggable from 'vuedraggable'

export default {
  components: {
    draggable
  },
  props: {
    value: {
      type: Array,
      default () {
        return []
      }
    }
  },
  data: () => {
    return {
      orderedItems: [],
      leftOverItems: []
    }
  },
  mounted () {
    this.orderedItems = this.value
    this.leftOverItems = this.$store.state.config.bootStates.filter(v => !this.orderedItems.some(e => e.id === v.id))
  }
}
</script>
