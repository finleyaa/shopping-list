<template>
  <form
    class="mx-auto flex w-full max-w-sm flex-col space-y-2 py-10"
    @submit.prevent="loginOrRegister"
  >
    <template v-if="!registering">
      <BaseInput
        v-model="login.email"
        placeholder="Email"
        type="email"
        required
      />
      <BaseInput
        v-model="login.password"
        placeholder="Password"
        type="password"
        required
      />
      <p
        v-if="loginError"
        class="text-red-500"
      >
        {{ loginError }}
      </p>
    </template>
    <template v-else>
      <BaseInput
        v-model="register.email"
        placeholder="Email"
        type="email"
        required
      />
      <BaseInput
        v-model="register.password"
        placeholder="Password"
        type="password"
        required
      />
      <BaseInput
        v-model="register.password_confirmation"
        placeholder="Confirm Password"
        type="password"
        required
      />
      <p
        v-if="registerError"
        class="text-red-500"
      >
        {{ registerError }}
      </p>
    </template>
    <BaseButton type="submit">
      {{ registering ? 'Register' : 'Login' }}
    </BaseButton>
    <p
      class="cursor-pointer text-center text-sm text-gray-500 underline"
      @click="toggleRegistering"
    >
      {{ registering ? 'Login' : 'Register' }}
    </p>
  </form>
</template>

<script>
export default {
  name: 'Login',
  data () {
    return {
      login: {
        email: '',
        password: ''
      },
      loginError: null,
      registering: false,
      register: {
        email: '',
        password: '',
        password_confirmation: ''
      },
      registerError: null
    }
  },
  created () {
    this.$store.state.items.all = []
  },
  methods: {
    toggleRegistering () {
      this.registering = !this.registering
      this.loginError = null
      this.registerError = null
      this.login = {
        email: '',
        password: ''
      }
      this.register = {
        email: '',
        password: '',
        password_confirmation: ''
      }
    },
    async loginOrRegister () {
      if (this.registering) {
        try {
          // register
          await this.$store.dispatch('auth/register', this.register)
          // if successful, login
          await this.$store.dispatch('auth/login', this.register)
        } catch (error) {
          this.registerError = error.response?.data?.message ?? error.message
          return
        }
      } else {
        try {
          // login
          await this.$store.dispatch('auth/login', this.login)
        } catch (error) {
          this.loginError = error.response?.data?.message ?? error.message
          return
        }
      }
      // if successful, redirect to the list
      this.$router.push({ name: 'list' })
    }
  }
}
</script>
