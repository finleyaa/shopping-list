import axios from 'axios'

const state = () => ({
  all: []
})

const getters = {}

const mutations = {
  set: (state, payload) => {
    state.all = payload
  }
}

const actions = {
  async get ({ commit }) {
    const items = (await axios({
      method: 'get',
      url: '/items',
      headers: {
        Accept: 'application/json'
      }
    })).data.data
    commit('set', items)
  },
  async create (_, payload) {
    await axios({
      method: 'post',
      url: '/items',
      headers: {
        Accept: 'application/json'
      },
      data: payload
    })
  },
  async delete (_, id) {
    await axios({
      method: 'delete',
      url: `/items/${id}`,
      headers: {
        Accept: 'application/json'
      }
    })
  },
  async update (_, payload) {
    await axios({
      method: 'patch',
      url: `/items/${payload.id}`,
      headers: {
        Accept: 'application/json'
      },
      data: payload
    })
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
