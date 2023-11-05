<template>
  <button class="rounded bg-blue-500 px-4 py-1 text-white hover:bg-blue-700">
    <slot />
  </button>
</template>

<script>
export default {
  name: 'BaseButton'
}
</script>
