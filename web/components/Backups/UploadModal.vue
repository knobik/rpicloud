<template>
  <b-modal ref="modal" :title="`Upload backup`" @hidden="$emit('hide')" @ok="submit">
    <b-form @submit.prevent="submit" @keydown="form.errors.clear($event.target.name)">
      <b-form-group label="Upload .img file (2GB max file size)">
        <b-form-file v-model="form.file" :class="{ 'is-invalid': form.errors.has('file') }" />
        <div v-if="form.errors.has('file')" class="invalid-feedback" v-text="form.errors.get('file')" />
      </b-form-group>
      <b-form-group label="or upload .img file from url">
        <b-form-input v-model="form.url" :class="{ 'is-invalid': form.errors.has('url') }" />
        <div v-if="form.errors.has('url')" class="invalid-feedback" v-text="form.errors.get('url')" />
      </b-form-group>
    </b-form>

    <template v-slot:modal-footer="{ ok, cancel }" :disabled="working">
      <b-button @click="cancel()">
        Cancel
      </b-button>
      <b-button variant="danger" :disabled="working" @click="ok()">
        <b-spinner v-if="working" small />
        Upload
      </b-button>
    </template>
  </b-modal>
</template>

<script>
import Form from '~/assets/js/utils/Form'

export default {
  props: {
    show: {
      type: Boolean,
      required: true
    }
  },
  data () {
    return {
      form: new Form({
        file: null,
        url: null
      }),
      working: false,
      nodes: []
    }
  },
  watch: {
    show (newValue) {
      if (newValue === true) {
        this.$refs.modal.show()
      } else {
        this.working = false
        this.$refs.modal.hide()
      }
    }
  },
  methods: {
    submit (e) {
      e.preventDefault()
      this.working = true

      this.form.post('/backups/upload')
        .then((response) => {
          this.working = false
          this.$refs.modal.hide()
          this.$emit('update', response.data.data)
        })
        .catch(() => {
          this.working = false
        })
    }
  }
}
</script>
