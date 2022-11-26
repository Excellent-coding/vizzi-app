<template>
    <div>
        <breadcrumb heading="Booth Management" />
        <div class="top-right-button-container d-flex" v-if="user && user.role < 3 && hallIndex == 1">
            <b-button variant="primary" v-b-modal.hall_modal>Create Hall</b-button>
        </div>
        <div class="separator mb-5"></div>
        <b-tabs v-model="hallIndex" v-if="!onView">
            <b-tab title="Management">
                <h5 class="mb-4 mt-2 card-title" v-if="activeItems.length">Active Booths</h5>
                <b-row>
                    <b-colxx xxs="12" xs="6" lg="4" v-for="item in activeItems" :key="item.id">
                        <b-card class="mb-4" no-body>
                            <b-card-header class="text-center booth-header mt-2">{{item.title}}</b-card-header>
                            <div class="position-relative" v-if="item.template">
                                <img :src="'/data/'+item.template.media" class="card-img-top" />
                                <template v-if="item.data">
                                    <div
                                        :style="itemStyle(element)"
                                        class="position-absolute" 
                                        v-for="(element, index) in JSON.parse(item.data)"
                                        :key="index"
                                    >
                                        <template v-if="element.url">
                                            <img :src="'/data/'+element.url" class="w-100 h-100" style="object-fit: cover" v-if="element.url.substring(0, 4) != 'http'" />
                                            <img :src="element.url" class="w-100 h-100" style="object-fit: cover" v-else />
                                        </template>
                                    </div>
                                </template>
                                <b-badge variant="primary" pill class="position-absolute badge-top-left" v-if="item.status == 2">Activated</b-badge>
                                <b-badge variant="warning" pill class="position-absolute badge-top-left" v-else>Deactivated</b-badge>
                                <b-badge variant="secondary" pill class="position-absolute badge-top-left-2" v-if="item.template.type == 0">Origin Template</b-badge>
                                <b-badge variant="info" pill class="position-absolute badge-top-left-2" v-else>Custom Template</b-badge>
                            </div>
                            <b-card-body class="py-1">
                                <div class="d-flex">
                                    <img :src="'/assets/img/avatar/'+item.user.avatar" class="user-avatar"/>
                                    <div class="my-auto ml-2">
                                        <h6 class="mb-0 card-subtitle">{{item.user.name}}</h6>
                                        <p class="mb-0">{{item.user.email}}</p>
                                    </div>
                                </div>
                                <p class="card-text text-muted text-small mb-0 font-weight-light">Created at {{item.created_at | moment('MM.DD.YYYY')}}</p>
                            </b-card-body>
                            <b-card-footer>
                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button variant="outline-primary" @click="edit(item)">Edit</b-button>
                                    <b-dropdown variant="primary" class="ml-2" right text="Action" v-if="user && user.role < 3">
                                        <b-dropdown-item size="sm" @click="reject(item.id)">Reject</b-dropdown-item>
                                        <b-dropdown-item size="sm" @click="deactivate(item.id)" v-if="item.status == 2">Deactivate</b-dropdown-item>
                                        <b-dropdown-item size="sm" @click="activate(item.id)" v-if="item.status == 3">Activate</b-dropdown-item>
                                        <b-dropdown-divider/>
                                        <b-dropdown-item size="sm" @click="remove(item.id)">Delete</b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </b-card-footer>
                        </b-card>
                    </b-colxx>
                </b-row>

                <h5 class="mb-4 card-title" v-if="pendingItems.length">Pending Booths</h5>
                <b-row>
                    <b-colxx xxs="12" xs="6" lg="4" v-for="item in pendingItems" :key="item.id">
                        <b-card class="mb-4" no-body>
                            <b-card-header class="text-center h4 mt-2">{{item.title}}</b-card-header>
                            <div class="position-relative" v-if="item.template">
                                <img :src="'/data/'+item.template.media" class="card-img-top" />
                                <template v-if="item.data">
                                    <div
                                        :style="itemStyle(element)"
                                        class="position-absolute" 
                                        v-for="(element, index) in JSON.parse(item.data)"
                                        :key="index"
                                    >
                                        <template v-if="element.url">
                                            <img :src="'/data/'+element.url" class="w-100 h-100" style="object-fit: cover" v-if="element.url.substring(0, 4) != 'http'" />
                                            <img :src="element.url" class="w-100 h-100" style="object-fit: cover" v-else />
                                        </template>
                                    </div>
                                </template>
                                <b-badge variant="primary" pill class="position-absolute badge-top-left" v-if="item.status == 1">Published</b-badge>
                                <b-badge variant="light" pill class="position-absolute badge-top-left" v-else>Working</b-badge>
                                <b-badge variant="secondary" pill class="position-absolute badge-top-left-2" v-if="item.template.type == 0">Origin Template</b-badge>
                                <b-badge variant="info" pill class="position-absolute badge-top-left-2" v-else>Custom Template</b-badge>
                            </div>
                            <b-card-body class="py-1">
                                <div class="d-flex">
                                    <img :src="'/assets/img/avatar/'+item.user.avatar" class="user-avatar"/>
                                    <div class="my-auto ml-2">
                                        <h6 class="mb-0 card-subtitle">{{item.user.name}}</h6>
                                        <p class="mb-0">{{item.user.email}}</p>
                                    </div>
                                </div>
                                <p class="card-text text-muted text-small mb-0 font-weight-light">Created at {{item.created_at | moment('MM.DD.YYYY')}}</p>
                            </b-card-body>
                            <b-card-footer>
                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button variant="outline-primary" @click="edit(item)">Edit</b-button>
                                    <b-dropdown variant="primary" class="ml-2" right text="Action" v-if="user && user.role < 3">
                                        <b-dropdown-item size="sm" @click="reject(item.id)">Reject</b-dropdown-item>
                                        <b-dropdown-item size="sm" @click="approve(item.id)">Approve</b-dropdown-item>
                                        <b-dropdown-divider/>
                                        <b-dropdown-item size="sm" @click="remove(item.id)">Delete</b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </b-card-footer>
                        </b-card>
                    </b-colxx>
                </b-row>

                <h5 class="mb-4 card-title" v-if="workingItems.length && user && user.role < 3">Working Booths</h5>
                <b-row>
                    <b-colxx xxs="12" xs="6" lg="4" v-for="item in workingItems" :key="item.id">
                        <b-card class="mb-4" no-body>
                            <b-card-header class="text-center h4 mt-2">{{item.title}}</b-card-header>
                            <div class="position-relative" v-if="item.template">
                                <img :src="'/data/'+item.template.media" class="card-img-top" />
                                <template v-if="item.data">
                                    <div
                                        :style="itemStyle(element)"
                                        class="position-absolute" 
                                        v-for="(element, index) in JSON.parse(item.data)"
                                        :key="index"
                                    >
                                        <template v-if="element.url">
                                            <img :src="'/data/'+element.url" class="w-100 h-100" style="object-fit: cover" v-if="element.url.substring(0, 4) != 'http'" />
                                            <img :src="element.url" class="w-100 h-100" style="object-fit: cover" v-else />
                                        </template>
                                    </div>
                                </template>
                                <b-badge variant="primary" pill class="position-absolute badge-top-left" v-if="item.status == 1">Published</b-badge>
                                <b-badge variant="light" pill class="position-absolute badge-top-left" v-else>Working</b-badge>
                                <b-badge variant="secondary" pill class="position-absolute badge-top-left-2" v-if="item.template.type == 0">Origin Template</b-badge>
                                <b-badge variant="info" pill class="position-absolute badge-top-left-2" v-else>Custom Template</b-badge>
                            </div>
                            <b-card-body class="py-1">
                                <div class="d-flex">
                                    <img :src="'/assets/img/avatar/'+item.user.avatar" class="user-avatar"/>
                                    <div class="my-auto ml-2">
                                        <h6 class="mb-0 card-subtitle">{{item.user.name}}</h6>
                                        <p class="mb-0">{{item.user.email}}</p>
                                    </div>
                                </div>
                                <p class="card-text text-muted text-small mb-0 font-weight-light">Created at {{item.created_at | moment('MM.DD.YYYY')}}</p>
                            </b-card-body>
                            <b-card-footer>
                                <div class="d-flex justify-content-end align-items-center">
                                    <b-button variant="outline-primary" @click="edit(item)">Edit</b-button>
                                    <b-button variant="primary" @click="remove(item.id)" class="ml-2" v-if="user && user.role < 3">Delete</b-button>
                                </div>
                            </b-card-footer>
                        </b-card>
                    </b-colxx>
                </b-row>
            </b-tab>
            <b-tab title="Hall Setting" v-if="user && user.role < 3">
                <div class="table-responsive">
                    <b-table
                        :fields="hallFields"
                        :items="hallData"
                        hover
                    >
                        <template v-slot:cell(#)="data">
                            <div class="mt-2">{{data.index + 1}}</div>
                        </template>
                        <template v-slot:cell(status)="data">
                            <b-badge class="mt-2" pill :variant="'info'" v-if="data.item.status == 0">Inactive</b-badge>
                            <b-badge class="mt-2" pill :variant="'primary'" v-if="data.item.status == 1">Active</b-badge>
                        </template>
                        <template v-slot:cell(view)="data">
                            <div class="my-auto">
                                <b-button variant="outline-primary" @click="data.toggleDetails"  class="my-auto" style="width: 120px">
                                    {{data.detailsShowing ? 'Hide' : 'Show'}} Items
                                </b-button>
                            </div>
                        </template>
                        <template v-slot:row-details="row">
                            <b-table
                                :fields="boothFields"
                                :items="row.item.booth_items"
                                hover
                            >
                                <template v-slot:cell(#)="data">
                                    <div class="mt-2">{{data.index + 1}}</div>
                                </template>
                                <template v-slot:cell(view)="data">
                                    <b-button variant="outline-primary" @click="viewBoothItem(data.item)">View</b-button>
                                </template>
                                <template v-slot:cell(action)="data">
                                    <b-button variant="primary" @click="delItem(data.item.id)">Delete</b-button>
                                </template>
                            </b-table>
                        </template>
                        <template v-slot:cell(action)="data">
                            <b-dropdown variant="primary" right text="Action">
                                <b-dropdown-item @click="editItem(data.item)">Edit</b-dropdown-item>
                                <hr>
                                <b-dropdown-item @click="hallStatus(data.item.id, 1)">Active</b-dropdown-item>
                                <b-dropdown-item @click="hallStatus(data.item.id, 0)">InActive</b-dropdown-item>
                                <b-dropdown-item @click="hallStatus(data.item.id, 2)">Remove</b-dropdown-item>
                            </b-dropdown>
                        </template>
                    </b-table>
                </div>
            </b-tab>
        </b-tabs>
        <div class="w-100" v-else>
            <b-button variant="primary" @click="onView = false" class="my-2">Return</b-button>
            <div class="position-relative" v-if="viewData.template">
                <img :src="'/data/'+viewData.template.media" class="w-100" />
                <template v-if="viewData.data">
                    <div
                        :style="itemStyle(element)"
                        class="position-absolute" 
                        v-for="(element, index) in JSON.parse(viewData.data)"
                        :key="index"
                    >
                        <template v-if="element.url">
                            <img :src="'/data/'+element.url" class="w-100 h-100" style="object-fit: cover" v-if="element.url.substring(0, 4) != 'http'" />
                            <img :src="element.url" class="w-100 h-100" style="object-fit: cover" v-else />
                        </template>
                    </div>
                </template>
            </div>
        </div>
        <b-modal ref="hall_modal" id="hall_modal" :title="hallTitle" centered>
            <b-form class="text-left">
                <b-form-group label="Hall Name">
                    <b-form-input v-model="hallItem.name"/>
                </b-form-group>
                <b-form-group label="Booth Titles" class="my-2">
                    <multiselect
                        v-model="hallItem.items"
                        :options="hallBoothItems"
                        :multiple="true"
                    />
                </b-form-group>
            </b-form>
            <template slot="modal-footer">
                <b-button variant="secondary" @click="hideModal('hall_modal')" class="mr-1">Cancel</b-button>
                <b-button variant="primary" @click="addHall()" class="ml-1">Confirm</b-button>
            </template>
        </b-modal>
        <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
            If you click confirm, you can't recover this data anymore!
            <template slot="modal-footer">
                <b-button variant="primary" @click="boothConfirm()" class="mr-1">Confirm</b-button>
                <b-button variant="secondary" @click="hideModal('confirm_modal')">Cancel</b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import exhibitMixins from '../exhibitMixins.js'

export default {
    metaInfo () {
        return { title: `Booth Setting` }
    },
    mixins: [exhibitMixins],
    data() {
        return {
            type: 0,
            text: 'booth',
        }
    },
    mounted() {
        this.boothInit()
    }
}
</script>