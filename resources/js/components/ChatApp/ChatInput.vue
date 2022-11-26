<template>
    <div class="chat-input" id="chat_input">
        <div class="input-box">
            <div
                contentEditable="true"
                class="text-area"
                @keyup.enter="getSize()"
                @keyup.enter.exact="submit()"
                @keyup.enter.shift.exact="shift()"
                id="chat_content"
            />
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        getSize() {
            this.$emit('changed', document.getElementById('chat_input').clientHeight)
        },
        shift() {},
        submit() {
            this.$emit('submitted', document.getElementById('chat_content').innerHTML)
            document.getElementById('chat_content').innerHTML = ''
        }
    }
}
</script>

<style lang="scss">

</style>