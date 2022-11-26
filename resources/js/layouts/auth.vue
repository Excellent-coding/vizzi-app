<template>
  <div id="root">
    <main>
      <div class="container text-center">
        <child />
      </div>
    </main>
    <footer>
      <auth-footer></auth-footer>
    </footer>
  </div>
</template>
<script>
import AuthFooter from '../components/Footer/AuthFooter';
export default {
  name: 'AuthLayout',
  mounted() {
    document.body.classList.add('background');
  },
  beforeDestroy() {
    document.body.classList.remove('background');
  },
  components: {
    'auth-footer': AuthFooter,
  },
};
</script>

<style lang="scss" scoped>
#root {
  background: #353d47;
}
</style>
