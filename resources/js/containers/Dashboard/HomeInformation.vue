<template>
    <b-row>
        <b-colxx lg="12" md="6" sm="12" class="mb-4">
            <list-card 
                title="Basic Information"
                :data="basicData"
            />
        </b-colxx>
        <b-colxx lg="12" md="6" sm="12" class="mb-4">
            <list-card 
                title="Stage Session Results"
                :data="sessionData"
            />
        </b-colxx>
    </b-row>
</template>

<script>
import ListCard from '../../components/Cards/ListCard'
export default {
    components: {
        ListCard
    },
    props: ['basicData', 'sessionData']
}
</script>