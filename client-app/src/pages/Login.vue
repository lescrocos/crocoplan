<template>
  <q-page padding class="row justify-center items-center">
    <div class="column col-sm-4 col-xs-10">
      <q-card>
        <q-form class="q-gutter-md" @submit="login">
          <q-card-section>
            <q-input v-model="email" type="email" label="Email"/>
            <q-input v-model="password" type="password" label="Mot de passe"/>
          </q-card-section>
          <q-card-actions class="q-px-md">
            <q-btn type="submit" color="primary" size="lg" class="full-width" label="Login"/>
          </q-card-actions>
        </q-form>
      </q-card>
    </div>
  </q-page>
</template>

<script lang="ts">
import Component from 'vue-class-component';
import Vue from 'vue';
import { authentificationService } from 'src/services/authentification.service';

@Component
export default class Login extends Vue {
  email = '';
  password = '';

  public login() {
    authentificationService.login(this.email, this.password);
  }
}
</script>
