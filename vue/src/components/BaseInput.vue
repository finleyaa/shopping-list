<template>
  <input
    class="rounded-md border border-gray-300 p-1"
    @input="$emit('update:modelValue', $event.target.value)"
  >
</template>

<script>
export default {
  name: 'BaseInput',
  props: {
    modelValue: {
      type: String,
      required: true
    }
  },
  emits: ['update:modelValue'],
  watch: {
    modelValue: {
      handler (value) {
        if (this.$el) this.$el.value = value
      },
      immediate: true
    }
  }
}
</script>
