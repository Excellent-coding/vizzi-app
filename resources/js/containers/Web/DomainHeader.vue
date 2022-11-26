<template>
    <b-colxx xxs="12">
        <div class="top-right-button-container d-flex" v-if="type == 'modal'">
            <b-add-button :initData="initData" v-if="type == 'modal'" />
            <b-status-button
                :selectAll="selectAll"
                :isSelectedAll="isSelectedAll" 
                :keymap="keymap"
                :isAnyItemSelected="isAnyItemSelected" 
                :setStatus="setStatus"
                :status="status"
            />
        </div>
        <breadcrumb :heading="title" />
        <div class="separator mb-5" />
    </b-colxx>
</template>

<script>
import AddButton from '../../components/Pages/AddButton'
import StatusButton from '../../components/Pages/StatusButton'

export default {
    components: {
        'b-add-button': AddButton,
        'b-status-button': StatusButton
    },
    props: [
        'title',
        'selectAll',
        'isSelectedAll',
        'keymap',
        'isAnyItemSelected',
        "setStatus",
        'initData',
        'status',
        'type'
    ]
}
</script>