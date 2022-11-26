<template>
    <b-row>
        <b-colxx sm="12" md="6" class="mb-4">
            <radial-progress-card title="Registrations" :content="users" color="text-success" :percent="24" />
        </b-colxx>
        <!-- <b-colxx sm="12" md="6" class="mb-4">
            <radial-progress-card title="Tickets sold" content="0" color="text-info" :percent="0" />
        </b-colxx> -->
    </b-row>
</template>

<script>
import RadialProgressCard from '../../components/Cards/RadialProgressCard'
export default {
    components: {
        RadialProgressCard
    },
    props: ['users']
}
</script>