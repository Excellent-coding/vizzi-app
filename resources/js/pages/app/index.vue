<template>
    <router-view />
</template>

<script>
export default {
    layout: 'app',
    middleware: 'auth'
}
</script>