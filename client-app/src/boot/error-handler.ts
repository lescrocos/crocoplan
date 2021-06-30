import { boot } from 'quasar/wrappers'
import { Notify } from 'quasar'

import Vue from 'vue'

export default boot(({ Vue }) => {
  // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
  Vue.config.errorHandler = (err: Error, vm: Vue, info: string) => {
    Notify.create({
      type: 'negative',
      message: err.message
    })
  }
})
