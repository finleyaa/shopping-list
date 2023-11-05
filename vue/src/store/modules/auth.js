import axios from 'axios'

const state = () => ({
  user: null
})

const getters = {
}

const mutations = {
  setToken: (state, payload) => {
    axios.defaults.headers.common.Authorization = `Bearer ${payload}`
  },
  setUser: (state, payload) => {
    state.user = payload
  }
}

const actions = {
  async register (_, payload) {
    await axios({
      method: 'post',
      url: '/register',
      headers: {
        Accept: 'application/json'
      },
      data: payload
    })
  },
  async login ({ commit }, payload) {
    const response = (await axios({
      method: 'post',
      url: '/login',
      headers: {
        Accept: 'application/json'
      },
      data: payload
    })).data.data
    commit('setUser', response.user)
    localStorage.setItem('token', response.token)
    commit('setToken', response.token)
  },
  async logout () {
    await axios({
      method: 'post',
      url: '/logout',
      headers: {
        Accept: 'application/json'
      }
    })
  },
  async update ({ commit }, payload) {
    const response = (await axios({
      method: 'patch',
      url: '/user',
      headers: {
        Accept: 'application/json'
      },
      data: payload
    })).data.data
    commit('setUser', response)
  },
  async me ({ commit }) {
    const response = (await axios({
      method: 'get',
      url: '/user',
      headers: {
        Accept: 'application/json'
      }
    })).data.data
    commit('setUser', response)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
