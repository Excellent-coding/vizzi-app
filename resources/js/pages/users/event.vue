<template>
<div>
    <b-row>
        <b-colxx class="disable-text-selection">
            <user-page-heading
                title="Event Stuff"
                :selectAll="selectAll"
                :isSelectedAll="isSelectedAll"
                :isAnyItemSelected="isAnyItemSelected"
                :keymap="keymap"
                :displayMode="displayMode"
                :changeDisplayMode="changeDisplayMode"
                :statusOptions="statusOptions"
                :setStatus="setStatus"
                :resend="resend"
                :modalClosed="modalClosed"
                :filter="filter"
                :from="from"
                :to="to"
                :total="total"
                :perPage="perPage"
                :changePageSize="changePageSize"
                :onFileChange="onFileChange"
                @reset-users="resetUsers"
            />
            <user-page-listing
                :displayMode="displayMode"
                :users="users"
                :selectedUsers="selectedUsers"
                :toggleItem="toggleItem"
                :modalTitle="modalTitle"
                :submitText="submitText"
                :edit="edit"
                :lastPage="lastPage"
                :perPage="perPage"
                :page="page"
                :changePage="changePage"
            />
        </b-colxx>
    </b-row>
    <user-action-modal
        :submitText="submitText"
        :modalTitle="modalTitle"
        :addNewItem="addNewItem"
        :isOpen="isOpen"
        :newItem="newItem"
        @init-item="initItem"
        @modal-closed="modalClosed"
    />  
    <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
        If you click confirm, you can't recover this data anymore!
        <template slot="modal-footer">
            <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
            <b-button variant="secondary" @click="$refs.confirm_modal.hide()">Cancel</b-button>
        </template>
    </b-modal>
</div>

</template>

<script>
import userMixins from '../../mixins/userMixins'

export default {
    mixins: [userMixins],
    data() {
        return {
            role: 6
        }
    }
}
</script>