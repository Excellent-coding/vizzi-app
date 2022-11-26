<template>
    <b-row>
        <b-colxx xxs="12">
            <h1>{{ title }}</h1>
            <div class="top-right-button-container d-flex">
                <b-dropdown
                    variant="primary"
                    text="CSV Upload"
                    size="lg"
                    v-if="user && user.role < 3"
                >
                    <label for="file" class="dropdown-item">Upload Users</label>
                    <a href="/assets/sample/user.xlsx" download class="dropdown-item">
                        Template Download
                    </a>
                </b-dropdown>
                <b-button
                    v-b-modal.user_modal
                    variant="primary"
                    size="lg"
                    class="top-right-button"
                >Add <span class="d-md-inline d-none">New</span></b-button>
                <b-button-group>
                    <b-dropdown split right @click="selectAll(true)" class="check-button" variant="primary">
                        <label
                            class="custom-control custom-checkbox pl-4 mb-0 d-inline-block"
                            slot="button-content"
                        >
                        <input
                            class="custom-control-input"
                            type="checkbox"
                            :checked="isSelectedAll"
                            v-shortkey="{select: ['ctrl','a'], undo: ['ctrl','d']}"
                            @shortkey="keymap"
                        />
                        <span
                            :class="{
                                'custom-control-label' :true,
                                'indeterminate' : isAnyItemSelected
                            }"
                        >&nbsp;</span>
                        </label>
                        <b-dropdown-item @click="setStatus(1)">Active</b-dropdown-item>
                        <b-dropdown-item @click="setStatus(0)">Inactive</b-dropdown-item>
                        <b-dropdown-item @click="setStatus(2)">Delete</b-dropdown-item>
                        <!-- <b-dropdown-divider />
                        <b-dropdown-item @click="resend">Resend Verification Code</b-dropdown-item> -->
                    </b-dropdown>
                </b-button-group>
            </div>
            <breadcrumb />
            <div class="mb-2 mt-2">
                <b-button
                    variant="empty"
                    class="pt-0 pl-0 d-inline-block d-md-none"
                    v-b-toggle.displayOptions
                >
                    Display Search Filters
                    <i class="simple-icon-arrow-down align-middle" />
                </b-button>
                <b-collapse id="displayOptions" class="d-md-block">
                    <span class="mr-3 d-inline-block float-md-left">
                        <a
                            :class="{'mr-2 view-icon':true,'active': displayMode==='list'}"
                            @click="changeDisplayMode('list')"
                        >
                            <data-list-icon />
                        </a>
                        <a
                            :class="{'mr-2 view-icon':true,'active': displayMode==='thumb'}"
                            @click="changeDisplayMode('thumb')"
                        >
                            <thumb-list-icon />
                        </a>
                    </span>
                    <div class="d-block d-md-inline-block pt-1">
                        <b-dropdown
                            id="ddown1"
                            :text="'Order By : ' + filterText"
                            variant="outline-dark"
                            class="mr-1 float-md-left btn-group"
                            size="xs"
                        >
                            <b-dropdown-item
                                v-for="status in statusOptions"
                                :key="status.value"
                                @click="filter.status = status.value, filterText = status.text"
                            >{{ status.text }}</b-dropdown-item>
                        </b-dropdown>
                        <div class="search-sm d-inline-block float-md-left mr-1 align-top">
                            <b-input placeholder="User Search" v-model="value"  @change="filter.input = value" />
                        </div>
                    </div>
                    <div class="float-md-right pt-1">
                        <span class="text-muted text-small mr-1 mb-2">{{from}}-{{to}} of {{ total }}</span>
                        <b-dropdown
                            id="ddown2"
                            right
                            :text="`${perPage}`"
                            variant="outline-dark"
                            class="d-inline-block"
                            size="xs"
                        >
                            <b-dropdown-item
                                v-for="(size,index) in pageSizes"
                                :key="index"
                                @click="changePageSize(size)"
                            >{{ size }}</b-dropdown-item>
                        </b-dropdown>
                    </div>
                </b-collapse>
            </div>
            <div class="separator mb-5" />
            <input id="file" type="file" @change="onFileChange" onclick="this.value = null" class="d-none" />
        </b-colxx>
    </b-row>
</template>
<script>
import {
    DataListIcon,
    ThumbListIcon
} from "../../components/Svg";
import { mapGetters } from 'vuex'

export default {
    components: {
        DataListIcon,
        ThumbListIcon
    },
    props: [
        "title",
        "selectAll",
        "isSelectedAll",
        "isAnyItemSelected",
        "keymap",
        "displayMode",
        "changeDisplayMode",
        "apiBase",
        "role",
        "siteId",
        "statusOptions",
        "setStatus",
        "resend",
        "filter",
        "from",
        "to",
        "total",
        "perPage",
        "changePageSize",
        "onFileChange"
    ],
    computed: mapGetters({
        user: 'auth/user'
    }),
    data() {
        return {
            filterText: 'All',
            pageSizes: [10, 20, 50],
            value: ''
        }
    }
}
</script>
