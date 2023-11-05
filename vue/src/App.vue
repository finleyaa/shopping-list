<template>
  <router-view />
</template>

<script>
export default {
  name: 'App',
  created () {
    const token = localStorage.getItem('token')
    if (token) {
      this.$store.commit('auth/setToken', token)
    }

    this.$store.subscribeAction({
      error: (action, state, error) => {
        if (error?.response?.data?.message === 'Unauthenticated.') {
          // if the user is offline, we don't care about this error
          this.$router.push({
            name: 'login'
          })
        }
      }
    })
  }
}
</script>
