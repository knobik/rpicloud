<template>
  <div class="app flex-row align-items-center">
    <div class="container">
      <b-row class="justify-content-center">
        <b-col md="5">
          <b-card-group>
            <b-card no-body class="p-4">
              <b-card-body>
                <b-form @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
                  <h1>Login</h1>
                  <p class="text-muted">
                    Sign In to your account
                  </p>
                  <b-input-group class="m-0 mb-3">
                    <b-input-group-prepend>
                      <b-input-group-text><i class="icon-user" /></b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-input
                      v-model="form.email"
                      name="email"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.has('email') }"
                      placeholder="email"
                    />
                    <div
                      v-if="form.errors.has('email')"
                      class="invalid-feedback"
                      v-text="form.errors.get('email')"
                    />
                  </b-input-group>
                  <b-input-group class="m-0 mb-4">
                    <b-input-group-prepend>
                      <b-input-group-text><i class="icon-lock" /></b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-input
                      v-model="form.password"
                      name="password"
                      type="password"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.has('password') }"
                      placeholder="password"
                    />
                    <div
                      v-if="form.errors.has('password')"
                      class="invalid-feedback"
                      v-text="form.errors.get('password')"
                    />
                  </b-input-group>
                  <b-row>
                    <b-col cols="6">
                      <b-button type="submit" variant="primary" class="px-4">
                        Login
                      </b-button>
                    </b-col>
                    <b-col cols="6" class="text-right">
                      <b-button variant="link" class="px-0">
                        Forgot password?
                      </b-button>
                    </b-col>
                  </b-row>
                </b-form>
              </b-card-body>
            </b-card>
          </b-card-group>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
import Form from '~/assets/js/utils/Form'
import Api from '~/assets/js/utils/Api'

export default {
  layout: 'auth',
  data () {
    return {
      form: new Form({
        email: '',
        password: ''
      })
    }
  },
  mounted () {
    // init the cookie
    Api.get('/auth/csrf-cookie')
  },
  methods: {
    onSubmit () {
      this.form.post('/auth/login').then((data) => {
        this.$store.commit('setAuthenticated', true)
        this.$router.push({ name: 'index' })
      })
    }
  }
}
</script>
