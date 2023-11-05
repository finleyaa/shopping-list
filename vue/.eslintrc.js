module.exports = {
  env: {
    browser: true,
    es2021: true,
    node: true
  },
  extends: [
    'standard',
    'plugin:vue/vue3-recommended',
    'plugin:tailwindcss/recommended'
  ],
  rules: {
    'vue/max-attributes-per-line': ['error', {
      singleline: {
        max: 1
      },
      multiline: {
        max: 1
      }
    }],
    'vue/html-closing-bracket-newline': ['error', {
      singleline: 'never',
      multiline: 'always'
    }],
    'vue/attribute-hyphenation': 'off',
    'vue/no-v-html': 'off',
    'vue/v-on-event-hyphenation': 'off',
    'tailwindcss/no-custom-classname': 'off'
  }
}
