<template>
  <q-page padding class="row justify-center items-center">
    <div class="column col-sm-4 col-xs-10">
      <q-card>
        <q-card-section>
          <q-form class="q-gutter-md">
            <q-input clearable v-model="email" type="email" label="Email" />
            <q-input clearable v-model="password" type="password" label="Mot de passe" />
          </q-form>
        </q-card-section>
        <q-card-actions class="q-px-md">
          <q-btn color="primary" size="lg" class="full-width" label="Login" @click="login"/>
        </q-card-actions>
      </q-card>
    </div>
  </q-page>
</template>

<script lang="ts">
import Component from 'vue-class-component';
import Vue from 'vue';
import { authentificationService } from 'src/services/authentification.service'

@Component
export default class Login extends Vue {
  email = ''
  password = ''

  public login() {
    authentificationService.login(this.email, this.password)
  }
}
</script>
