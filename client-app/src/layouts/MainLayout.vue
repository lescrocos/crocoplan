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

        <div v-if="authentificationService.state.utilisateur">{{ authentificationService.state.utilisateur.nom }}</div>

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

import { Vue, Component } from 'vue-property-decorator'
import { familleStore } from 'src/store/famille.store'
import { Famille } from 'src/interfaces/famille'
import { familleService } from 'src/services/famille.service'
import { authentificationService } from 'src/services/authentification.service'
import { Notify } from 'quasar';

Vue.config.errorHandler = (err: Error, vm, info: string) => {
  console.log('toto ' + err)
  Notify.create({
    type: 'negative',
    message: err.message
  })
}

@Component({
  components: { EssentialLink, Login }
})
export default class MainLayout extends Vue {
  leftDrawerOpen = false;
  essentialLinks = linksData;
  familleStore = familleStore;
  familles: Famille[] | null = null
  authentificationService = authentificationService

  async created () {
    this.familles = await familleService.findAll()
  }

  changeFamille (famille: Famille) {
    familleStore.selectionneFamille(famille)
  }
}
</script>
