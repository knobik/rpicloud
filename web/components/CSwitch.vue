<script>
import { Switch } from '@coreui/vue'
// this is a component to fix the default behavior of coreui switch which emits a "change" event on mount.
export default {
  extends: Switch,
  data () {
    return {
      firstChange: true
    }
  },
  methods: {
    toggle (checked) {
      this.checkedData = checked ? this.value : this.uncheckedValue

      if (!this.firstChange) {
        this.$emit('change', this.checkedData)
      }
      this.firstChange = false
    }
  }
}
</script>
