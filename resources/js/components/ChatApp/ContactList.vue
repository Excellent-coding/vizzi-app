<template>
    <div :style="getStyle()">
        asdf
    </div>
</template>

<script>
export default {
    props: ['height'],
    methods: {
        getStyle() {
            return {
                'height': 'calc(100vh - ' + this.height + 'px)',
                'width': '260px'
            }
        }
    }
}
</script>