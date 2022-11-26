<template>
  <b-row class="vh-100">
      <b-colxx xxs="12" md="10" class="mx-auto my-auto">
            <router-view />
      </b-colxx>
  </b-row>
</template>

<script>

export default {
  layout: 'auth',
  middleware: 'guest'
};
</script>