<template>
  <b-navbar toggleable="sm" class="nav-container">
    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

    <b-collapse id="nav-collapse" is-nav class="nav-left">
      <b-navbar-nav>
        <b-nav-item href="#">Terms &amp; Conditions</b-nav-item>
        <b-nav-item href="#">Privacy Policy</b-nav-item>
        <b-nav-item href="#">Anti-Bullying Policy</b-nav-item>
      </b-navbar-nav>

      <!-- Right aligned nav items -->
      <b-navbar-nav class="ml-auto">
        <b-nav-text right class="powered-by">Powered by Vizzi</b-nav-text>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script></script>
<style lang="scss" scoped>
.nav-container {
  background: transparent;
  box-shadow: none;
  .nav-left {
    .nav-link {
      color: #a4aeb5;
    }
  }

  .powered-by {
    padding-right: 8px;
    color: #a4aeb5;
  }
}
</style>
