<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="leftDrawerOpen = !leftDrawerOpen"
          v-if="authentificationService.state.utilisateur"
        />

        <q-toolbar-title>
          Planning Crocos
        </q-toolbar-title>

        <q-btn-dropdown v-if="authentificationService.state.utilisateur" :label="authentificationService.state.utilisateur.nom">
          <q-list>
            <q-item clickable @click="logout">
              <q-item-section>Se déconnecter</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>

        <div hidden="hidden">Quasar v{{ $q.version }}</div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      content-class="bg-grey-1"
      v-if="authentificationService.state.utilisateur"
    >
      <q-list>
        <q-item-label
          header
          class="text-grey-8"
        >
          Menu
        </q-item-label>
        <EssentialLink
          v-for="link in essentialLinks"
          :key="link.title"
          v-bind="link"
        />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view v-if="authentificationService.state.utilisateur"/>
      <Login v-else>
      </Login>
    </q-page-container>
  </q-layout>
</template>

<script lang="ts">
import EssentialLink from 'components/EssentialLink.vue'
import Login from 'pages/Login.vue'
import { Component, Vue } from 'vue-property-decorator'
import { authentificationService } from 'src/services/authentification.service'

const linksData = [
  {
    title: 'Mes gardes',
    caption: 'Liste des gardes qui me sont attribuées',
    icon: 'perm_contact_calendar',
    link: '/mes-gardes'
  },
  {
    title: 'Appels à gardes',
    caption: 'Liste des appels à garde',
    icon: 'notification_important',
    link: '/appels-a-garde'
  },
  {
    title: 'Mes dispos',
    caption: 'Mes disponibilités de garde pour un mois donné',
    icon: 'playlist_add_check',
    link: '/mes-dispos'
  },
  {
    title: 'Planning',
    caption: 'Planning hebdo',
    icon: 'date_range',
    link: '/planning'
  }
]

@Component({
  components: { EssentialLink, Login }
})
export default class MainLayout extends Vue {
  leftDrawerOpen = false
  essentialLinks = linksData
  authentificationService = authentificationService

  logout() {
    authentificationService.logout()
  }
}
</script>
