import { createStore } from 'vuex'
import auth from '@/store/modules/auth'
import items from '@/store/modules/items'

const store = createStore({
  state () {
    return {
    }
  },
  getters: {
  },
  mutations: {
  },
  actions: {
  },
  modules: {
    auth,
    items
  }
})

export default store
