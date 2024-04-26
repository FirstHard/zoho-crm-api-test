<template>
  <div v-if="message" :class="[alertType, 'alert alert-dismissible fade show']" role="alert">
    <button type="button" class="btn-close" @click="dismissAlert" aria-label="Close"></button>
    {{ message }}
  </div>
</template>

<script>
export default {
  name: "BootstrapAlert",
  props: {
    message: String,
    type: {
      type: String,
      default: 'danger'
    }
  },
  computed: {
    alertType() {
      return 'alert-' + this.type;
    }
  },
  methods: {
    dismissAlert() {
      this.$emit('dismiss');
    }
  }
};
</script>
