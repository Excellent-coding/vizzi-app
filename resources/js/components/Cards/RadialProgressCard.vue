<template>
<b-card no-body>
    <b-card-body class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-2 card-title" v-html="title" />
            <h1 v-html="content" class="mb-0 pb-0" :class="color" />
        </div>
        <div class="progress-bar-circle">
            <radial-progress-bar
                :diameter="54"
                :strokeWidth="2"
                :innerStrokeWidth="2"
                :completed-steps="percent"
                :total-steps=100
            >
                {{percent}}%
            </radial-progress-bar>
        </div>
    </b-card-body>
</b-card>
</template>

<script>
import RadialProgressBar from 'vue-radial-progress'
export default {
    props: ['title', 'content', 'percent', 'color'],
    components: {
        'radial-progress-bar' : RadialProgressBar
    }
}
</script>