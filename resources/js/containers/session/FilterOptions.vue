<template>
    <div>
        <template v-if="!agendaFilter">
            <b-dropdown
                id="ddwn1"
                :text="'Filter By : ' + filterText"
                class="mr-1 float-md-left btn-group"
                variant="outline-dark"
                :style="styleObject"
                size="xs"
            >
                    <b-dropdown-item
                        v-for="status in statusOptions"
                        :key="status.value"
                        @click="filterText = status.text, $emit('set-value', status.value)"
                    >{{ status.text }}</b-dropdown-item>
            </b-dropdown>
        </template>
        <template v-else>
            <button id="ddwn1" class="btn btn-outline-dark btn-xs filter-btn" :style="styleObject">Filter Of {{dayList[filterValue]}}</button>
        </template>
    </div>

</template>

<script>
export default {
    props: ['statusOptions', 'styleObject', 'set-value', 'agendaFilter', 'filterValue'],
    data() {
        this.$emit('set-value', 2)

        return {
            filterText: 'Date',
            dayList: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thirsday', 'Friday', 'Saturday']
        }
    },
}
</script>

<style lang="css">
    .filter-btn:hover {
        background: var(--color);
    }
</style>