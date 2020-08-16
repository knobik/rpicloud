<template>
  <div class="app">
    <c-confirm></c-confirm>
<!--    <vue-confirm-dialog></vue-confirm-dialog>-->
    <AppHeader fixed>
      <SidebarToggler class="d-lg-none" display="md" mobile />
      <b-link class="navbar-brand" to="#">
        <img class="navbar-brand-full" src="/img/brand/logo.svg" width="89" height="25" alt="CoreUI Logo">
        <img class="navbar-brand-minimized" src="/img/brand/sygnet.svg" width="30" height="30" alt="CoreUI Logo">
      </b-link>
      <SidebarToggler class="d-md-down-none" display="lg" />
      <b-navbar-nav class="ml-auto">
        <b-nav-item class="d-md-down-none" @click="logout">
          <i class="icon-logout" />
        </b-nav-item>
      </b-navbar-nav>
    </AppHeader>
    <div class="app-body">
      <AppSidebar fixed>
        <SidebarHeader />
        <SidebarForm />
        <SidebarNav :nav-items="nav" />
        <SidebarFooter />
        <SidebarMinimizer />
      </AppSidebar>
      <main class="main">
        <div class="container-fluid pt-5">
          <router-view />
        </div>
      </main>
    </div>
    <TheFooter>
      <!--footer-->
      <div>
        <span class="ml-1">&copy; 2020 Knobik</span>
      </div>
      <div class="ml-auto">
        <span class="mr-1">Admin template powered by</span>
        <a href="https://coreui.io">CoreUI for Vue</a>
      </div>
    </TheFooter>
  </div>
</template>

<script>
import { Header as AppHeader, SidebarToggler, Sidebar as AppSidebar, SidebarFooter, SidebarForm, SidebarHeader, SidebarMinimizer, SidebarNav, Footer as TheFooter } from '@coreui/vue'
import Api from '~/assets/js/utils/Api'
import CConfirm from '~/components/CConfirm.vue'

export default {
  middleware: 'authenticated',
  components: {
    CConfirm,
    AppHeader,
    AppSidebar,
    TheFooter,
    SidebarForm,
    SidebarFooter,
    SidebarToggler,
    SidebarHeader,
    SidebarNav,
    SidebarMinimizer
  },
  data () {
    return {
      nav: [
        {
          name: 'Nodes',
          url: '/nodes',
          icon: 'fa fa-database'
        },
        {
          name: 'Backups',
          url: '/backups',
          icon: 'fa fa-hdd-o'
        }
      ]
    }
  },
  computed: {
    name () {
      return this.$route.name
    },
    list () {
      return this.$route.matched.filter(route => route.name || route.meta.label)
    }
  },
  created () {
    if (!this.$store.state.config) {
      Api.get('/config').then((response) => {
        this.$store.commit('setConfig', response.data.data)
      })
    }
  },
  methods: {
    logout () {
      Api.post('/auth/logout').then(() => {
        this.$store.commit('setAuthenticated', false)
        this.$router.push({ name: '/auth/login' })
      })
    }
  }
}
</script>
