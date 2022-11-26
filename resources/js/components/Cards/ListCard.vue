<template>
    <b-card :title="title">
        <div v-for="(item, index) in data" :key="index">
            <div class="d-flex">
                <h6 class="text-muted" v-html="item.title" />
                <h6 class="ml-auto" v-html="item.value" />
            </div>
            <hr />
        </div>
    </b-card>
</template>

<script>
export default {
    props: ['title', 'data']
}
</script>