<template>
  <div class="mx-auto h-screen w-full max-w-sm space-y-6 py-10">
    <div class="flex flex-row items-center justify-between gap-2">
      <p class="text-sm text-gray-500">
        {{ $store.state.auth.user?.email }}
      </p>
      <BaseButton
        class="ml-auto"
        @click="logout"
      >
        Logout
      </BaseButton>
    </div>
    <div class="flex flex-row items-center">
      <h1 class="text-2xl font-bold">
        Your List
      </h1>
      <a
        class="ml-auto flex cursor-pointer flex-row gap-2 text-gray-500 transition-colors hover:text-gray-800"
        :href="mailTo"
      >
        <ShareIcon class="h-5 w-5" />
        <p class="text-sm">
          Share to email
        </p>
      </a>
    </div>

    <div class="space-y-2">
      <p>Add New Item</p>
      <form
        class="flex w-full flex-row gap-2"
        @submit.prevent="addItem"
      >
        <BaseInput
          v-model="newItem.name"
          placeholder="Item"
          type="text"
          class="grow"
          required
        />
        <BaseInput
          v-model="newItem.price"
          placeholder="Price"
          type="number"
          min="0"
          step="0.01"
          class="w-20"
        />
        <BaseButton type="submit">
          Add
        </BaseButton>
        <input
          hidden
          type="submit"
        >
      </form>
    </div>

    <ul
      ref="list"
      class="space-y-1 border-t border-gray-300 pt-6"
    >
      <li v-if="!items.length">
        <p class="text-center text-gray-500">
          No items yet
        </p>
      </li>
      <template v-else>
        <li
          v-for="item in items"
          :key="item.id"
          :data-id="item.id"
          class="flex w-full flex-row items-center gap-2"
        >
          <MenuAlt4Icon class="h-4 w-4 cursor-pointer text-gray-400" />
          <BaseInput
            type="checkbox"
            :checked="item.purchased"
            @change="toggleItemPurchased(item.id)"
          />
          <p
            class="mr-auto"
            :class="{
              'line-through': item.purchased,
            }"
          >
            {{ item.name }}
          </p>
          <p
            v-if="item.price !== null"
            class="text-sm text-gray-500"
            :class="{
              'line-through': item.purchased,
            }"
          >
            £{{ item.price }}
          </p>
          <MinusCircleIcon
            class="h-5 w-5 cursor-pointer text-red-400"
            @click="deleteItem(item.id)"
          />
        </li>
      </template>
    </ul>
    <div
      v-if="items.length"
      class="flex flex-row items-center justify-between"
    >
      <p class="font-medium">
        Total
      </p>
      <p>£{{ total }}</p>
    </div>
    <div class="grid grid-cols-2 items-center justify-between gap-y-2">
      <p class="font-medium">
        Spend Limit
      </p>
      <div class="flex flex-row items-center gap-2">
        <p class="text-gray-500">
          £
        </p>
        <BaseInput
          v-model="spendLimit"
          type="number"
          min="0"
          step="0.01"
          class="inline text-right"
          @change="debouncedSpendLimitChange"
        />
      </div>
      <template v-if="spendLimit !== null && spendLimit !== undefined">
        <p
          class="font-medium"
          :class="{'text-red-500': remaining < 0}"
        >
          Remaining
        </p>
        <p
          class="text-right"
          :class="{'text-red-500': remaining < 0}"
        >
          £{{ remaining }}
        </p>
      </template>
    </div>
  </div>
</template>

<script>
import { MinusCircleIcon } from '@heroicons/vue/solid'
import { ShareIcon, MenuAlt4Icon } from '@heroicons/vue/outline'
import Sortable from 'sortablejs'

export default {
  name: 'ShoppingList',
  components: {
    MinusCircleIcon,
    ShareIcon,
    MenuAlt4Icon
  },
  data () {
    return {
      newItem: {
        name: '',
        price: null
      },
      formError: null,
      sortable: null,
      spendLimit: null,
      debouncedTimeout: null
    }
  },
  computed: {
    items () {
      const items = this.$store.state.items.all
      items.sort((a, b) => a.order - b.order)
      return items
    },
    total () {
      return this.items
        .reduce((acc, item) => acc + +item.price, 0)
        .toFixed(2)
    },
    remaining () {
      return (this.spendLimit - this.total).toFixed(2)
    },
    mailTo () {
      const unpurchasedItems = this.items.filter(item => !item.purchased)
      return `mailto:?subject=Shopping%20List&body=Here%20is%20my%20shopping%20list%3A%0A%0A${unpurchasedItems.map(item => '%E2%80%A2%20' + item.name).join('%0A')}`
    }
  },
  async created () {
    // first get the current user
    await this.$store.dispatch('auth/me')
    this.spendLimit = this.$store.state.auth.user.spend_limit
    // then get the user's items
    this.$store.dispatch('items/get')
  },
  mounted () {
    const itemsWatcher = this.$watch(() => this.items, () => {
      if (this.items.length) {
        this.$nextTick(() => {
          this.sortable = Sortable.create(this.$refs.list, {
            onEnd: this.reorderItems
          })
          itemsWatcher() // stop watching
        })
      }
    })
  },
  methods: {
    addItem () {
      if (!this.newItem.name) {
        this.formError = 'Please enter a name'
        return
      }
      this.$store.dispatch('items/create', this.newItem)
        .then(() => {
          // reset the form
          this.newItem = {
            name: '',
            price: null
          }
          this.formError = null
          this.$store.dispatch('items/get')
        })
        .catch(error => {
          this.formError = error.response.data.message
        })
    },
    deleteItem (id) {
      this.$store.dispatch('items/delete', id)
        .then(() => {
          this.$store.dispatch('items/get')
        })
        .catch(error => {
          this.formError = error.response.data.message
        })
    },
    toggleItemPurchased (id) {
      this.$store.dispatch('items/update', {
        id,
        purchased: !this.items.find(item => item.id === id).purchased
      })
        .then(() => {
          this.$store.dispatch('items/get')
        })
        .catch(error => {
          this.formError = error.response.data.message
        })
    },
    reorderItems (evt) {
      const item = this.items.find(item => item.id === Number(evt.item.dataset.id))
      const order = evt.newIndex
      console.log(order)
      this.$store.dispatch('items/update', {
        id: item.id,
        order
      })
        .then(() => {
          this.$store.dispatch('items/get')
        })
        .catch(error => {
          this.formError = error.response.data.message
        })
    },
    debouncedSpendLimitChange () {
      if (this.debouncedTimeout) clearTimeout(this.debouncedTimeout)
      // users may experience issues if they refresh quickly after typing
      // but it's safe to assume that they won't be refreshing that quickly
      this.debouncedTimeout = setTimeout(async () => {
        this.$store.dispatch('auth/update', {
          spend_limit: this.spendLimit
        })
      }, 500)
    },
    async logout () {
      await this.$store.dispatch('auth/logout')
      this.$router.push({ name: 'login' })
    }
  }
}
</script>
