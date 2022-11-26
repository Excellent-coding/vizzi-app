<template>
<b-card class="d-flex flex-row" no-body>
    <div class="d-flex" v-if="data.category">
        <div v-if="data.type == 1 && data.media" @click="viewThumb('/data/' + data.media)">
            <img :src="'/data/' + data.media" class="list-thumbnail responsive border-0" :alt="data.title" v-if="typeImg(data.media)" />
            <iframe :src="'/data/' + data.media" class="list-thumbnail responsive border-0" :alt="data.title" v-else />
        </div>
        <div v-else-if="data.type == 2 && data.media && JSON.parse(data.media).length" @click="viewThumb('/data/' + JSON.parse(data.media)[0])">
            <img :src="'/data/' + JSON.parse(data.media)[0]" class="list-thumbnail responsive border-0" :alt="data.title" v-if="typeImg(JSON.parse(data.media)[0])" />
            <iframe :src="'/data/' + JSON.parse(data.media)[0]" class="list-thumbnail responsive border-0" :alt="data.title" v-else/>
        </div>
        <div v-else @click="viewThumb('/assets/img/poster-backdrop/' + data.category.backdrop.media)" >
            <img :src="'/assets/img/poster-backdrop/' + data.category.backdrop.media" class="list-thumbnail responsive border-0" :alt="data.title" v-if="typeImg(data.category.backdrop.media)" />
            <iframe :src="'/assets/img/poster-backdrop/' + data.category.backdrop.media" class="list-thumbnail responsive border-0" :alt="data.title" v-else />
        </div>
    </div>
    <div class="pl-2 d-flex flex-grow-1 min-width-zero">
        <div class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
            <div class="w-40 w-sm-100">
                <p class="list-item-heading mb-0 truncate">{{data.title}}</p>
            </div>
            <template v-if="data.category">
                <p class="mb-0 text-muted text-small w-15 w-sm-100 ml-auto" v-if="data.category">{{data.category.category}}</p>
                <p class="mb-0 text-muted text-small w-15 w-sm-100 ml-auto">{{data.created_at | moment('MMMM Do YYYY')}}</p>
                <div class="w-15 w-sm-100 text-right">
                    <b-badge pill variant="danger" v-if="data.status == 2">Published</b-badge>
                    <b-badge pill variant="primary" v-else-if="data.status == 1">Activated</b-badge>
                    <b-badge pill variant="info" v-else>Working</b-badge>
                </div>
            </template>
        </div>
        <b-button variant="primary" size="sm" class="my-auto mx-1" @click="select(data)" style="max-width: 130px;width: 100%">Edit this poster</b-button>
        <b-button variant="outline-primary" size="sm" class="my-auto ml-1 mr-3" @click="viewPoster(data)">View</b-button>
        <b-dropdown variant="primary" right size="sm" text="Action" class="mx-3 my-auto" v-if="user && user.role < 3">
            <b-dropdown-item @click="setStatus(data.id, 1)">Active</b-dropdown-item>
            <b-dropdown-item @click="setStatus(data.id, 2)">Publish</b-dropdown-item>
            <b-dropdown-item @click="setStatus(data.id, 0)">Reject</b-dropdown-item>
            <b-dropdown-item @click="setStatus(data.id, 3)">Remove</b-dropdown-item>
        </b-dropdown>
    </div>
</b-card>
</template>

<script>
export default {
    props: ['data', 'select', 'setStatus', 'user', 'viewPoster', 'viewThumb', 'typeImg'],
}
</script>
