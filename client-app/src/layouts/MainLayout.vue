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
        />

        <q-toolbar-title>
          Planning Crocos
        </q-toolbar-title>

        <q-select filled :value="familleStore.state.familleSelectionnee" :options="familles" label="Famille" option-label="nom" @input="changeFamille" />

        <div>Quasar v{{ $q.version }}</div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      content-class="bg-grey-1"
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
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script lang="ts">
import EssentialLink from 'components/EssentialLink.vue'

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
]

import { Vue, Component } from 'vue-property-decorator'
import { familleStore } from 'src/store/famille.store';
import { Famille } from 'src/interfaces/famille';
import { familleService } from 'src/services/famille.service';

@Component({
  components: { EssentialLink }
})
export default class MainLayout extends Vue {
  leftDrawerOpen = false;
  essentialLinks = linksData;
  familleStore = familleStore;
  familles: Famille[] | null = null

  async created() {
    this.familles = await familleService.findAll()
  }

  changeFamille(famille: Famille) {
    familleStore.selectionneFamille(famille)
  }

}
</script>
