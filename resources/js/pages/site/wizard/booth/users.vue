<template>
<div>
    <b-row>
        <b-colxx xxs="12">
            <div class="top-right-button-container d-flex">
                <a href="/assets/sample/exhibit.xlsx" download class="btn btn-outline-primary mr-2">Sample file Download</a>
                <label for="file" class="btn btn-primary mb-0">Upload</label>
            </div>
            <breadcrumb heading="Booth Team Member Information" />
            <div class="separator mb-5"></div>
        </b-colxx>
    </b-row>
    <b-card>
        <div class="table-responsive">
            <b-table
                :fields="userFields"
                :items="userBooths"
                hover
            >
                <template v-slot:cell(#)="data">{{data.index + 1}}</template>
                <template v-slot:cell(title)="data">
                    <div class="" style="width: 180px">{{data.item.title.substring(0, 120)}}</div>
                </template>
                <template v-slot:cell(status)="data">
                    <b-badge class="mt-2" pill :variant="'info'" v-if="data.item.status == 0">Working</b-badge>
                    <b-badge class="mt-2" pill :variant="'secondary'" v-if="data.item.status == 1">Pending</b-badge>
                    <b-badge class="mt-2" pill :variant="'primary'" v-if="data.item.status == 2">Active</b-badge>
                    <b-badge class="mt-2" pill :variant="'danger'" v-if="data.item.status == 3">Disable</b-badge>
                </template>
                <template v-slot:cell(mainManager)="data">
                    <div class="d-flex">
                        <img :src="'/assets/img/avatar/' + data.item.user.avatar" class="user-avatar mr-2" :alt="data.item.user.name" />
                        <div class="my-auto mr-auto">
                            <h6>{{data.item.user.name}}</h6>
                            <p class="mb-0">{{data.item.user.email}}</p>
                        </div>
                        <button class="btn btn-outline-primary" @click="data.toggleDetails" style="width: 180px">
                            {{data.detailsShowing ? 'Hide' : 'Show'}} Team Members
                        </button>
                    </div>
                </template>
                <template v-slot:row-details="row">
                    <b-table
                        :fields="memberFields"
                        :items="row.item.members"
                        hover
                    >
                        <template v-slot:cell(#)="data">{{data.index + 1}}</template>
                        <template v-slot:cell(status)="data">
                            <b-badge class="mt-2" pill :variant="'danger'" v-if="data.item.status == 0">Pending</b-badge>
                            <b-badge class="mt-2" pill :variant="'primary'" v-if="data.item.status == 1">Active</b-badge>
                        </template>
                        <template v-slot:cell(action)="data">
                            <b-dropdown variant="primary" right text="Action">
                                <b-dropdown-item @click="userStatus(data.item.id, 1)">Active</b-dropdown-item>
                                <b-dropdown-item @click="userStatus(data.item.id, 0)">InActive</b-dropdown-item>
                            </b-dropdown>
                        </template>
                    </b-table>
                </template>
                <template v-slot:cell(action)="data">
                    <b-dropdown variant="primary" right text="Action" v-if="data.item.status > 0">
                        <b-dropdown-item @click="boothStatus(data.item.id, 2)">Active</b-dropdown-item>
                        <b-dropdown-item @click="boothStatus(data.item.id, 3)">InActive</b-dropdown-item>
                        <b-dropdown-item @click="boothStatus(data.item.id, 0)">Reject</b-dropdown-item>
                    </b-dropdown>
                </template>
            </b-table>
        </div>
        <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal()">Cancel</b-button>
            </template>
        </b-modal>
    </b-card>
    <input type="file" @change="onFileChange" id="file" class="d-none" />
</div>

</template>

<script>
import exhibitMixins from '../exhibitMixins.js'

export default {
    metaInfo () {
        return { title: `Booth userMembers` }
    },
    mixins: [exhibitMixins],
    data() {
        return {
            type: 0
        }
    },
    mounted() {
        this.userInit()
    },
    methods: {
        onFileChange(e) {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length) return;
            this.createInput(files[0]);
        },
        createInput(file) {
            var reader = new FileReader();
            reader.onload = (e) => {
                var data = new Uint8Array(e.target.result);
                var workbook = XLSX.read(data, {type: 'array'});
                let sheetName = workbook.SheetNames[0]
                let worksheet = workbook.Sheets[sheetName];
                this.userSubmit(worksheet);
            }
            reader.readAsArrayBuffer(file);
        }
    }
}
</script>