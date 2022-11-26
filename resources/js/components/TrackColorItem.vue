<template>
    <div :style="'width:16px; height: 16px; border-radius: 50%;cursor: pointer;background:' + color" class="mr-2" />
</template>

<script>
export default {
    props: ['color']
}
</script>