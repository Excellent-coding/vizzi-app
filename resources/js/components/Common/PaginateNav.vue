<template>
    <b-row v-if="lastPage > 1">
        <b-pagination-nav
            :number-of-pages="lastPage"
            :link-gen="linkGen"
            :value="page"
            @change="(a)=>changePage(a)"
            per-page="5"
            align="center"
        >
            <template v-slot:next-text>
                <i class="simple-icon-arrow-right" />
            </template>
            <template v-slot:prev-text>
                <i class="simple-icon-arrow-left" />
            </template>
            <template v-slot:first-text>
                <i class="simple-icon-control-start" />
            </template>
            <template v-slot:last-text>
                <i class="simple-icon-control-end" />
            </template>
        </b-pagination-nav>
    </b-row>
</template>

<script>
export default {
    props: [
        'lastPage', 'page', 'changePage'
    ],
    methods: {
        linkGen(pageNum) {
            return "#";
        }, 
    }
}
</script>