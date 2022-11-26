<template>
    <div class="head">
        <img :src="'/data/' + logo" class="logo border-rounded" v-if="logo" />
        <div v-html="getTitle()" class="logo chat-header" v-else />
        <div class="text text-dark">
            <span>{{title}}</span>
            <div class="user" v-if="user">
                {{user.name}}
                <span size="7" class="status" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['title', 'logo', 'user'],
    methods: {
        getTitle() {
            return this.title.charAt(0).toUpperCase() + this.title.charAt(1)
        }
    }
}
</script>
