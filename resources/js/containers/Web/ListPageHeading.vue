<template>
  <b-row>
    <b-colxx xxs="12">
        <h1>{{ title }}</h1>
        <div class="top-right-button-container d-flex" v-if="user && user.role < 3 && tabIndex == 1">
            <b-button variant="primary" v-b-modal.hall_modal>Create Hall</b-button>
        </div>
        <breadcrumb />
        <div class="mb-2 mt-2" v-if="tabIndex == 0">
            <b-button
                variant="empty"
                class="pt-0 pl-0 d-inline-block d-md-none"
                v-b-toggle.displayOptions
            >DIsplay Options<i class="simple-icon-arrow-down align-middle" />
            </b-button>
            <b-collapse id="displayOptions" class="d-md-block">
            <span class="mr-3 d-inline-block float-md-left">
                <a
                    :class="{'mr-2 view-icon':true,'active': displayMode==='image'}"
                    @click="changeDisplayMode('image')"
                >
                    <image-list-icon />
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
                    text="Select by Category"
                    variant="outline-dark"
                    class="mr-1 float-md-left btn-group"
                    size="xs"
                >
                    <b-dropdown-item @click="changeOrderBy(0, 'category')">All</b-dropdown-item>
                    <b-dropdown-item
                        v-for="(category, index) in categoryData"
                        :key="index"
                        @click="changeOrderBy(category.id, 'category')"
                    >{{ category.category }}</b-dropdown-item>
                </b-dropdown>
                <b-dropdown
                    id="ddown1"
                    text="Select by Order Number"
                    variant="outline-dark"
                    class="mr-1 float-md-left btn-group"
                    size="xs"
                >
                    <b-dropdown-item @click="changeOrderBy('all', 'number')">All</b-dropdown-item>
                    <b-dropdown-item
                        v-for="(entry, index) in entryData"
                        :key="index"
                        @click="changeOrderBy(entry, 'number')"
                    >{{ entry }}</b-dropdown-item>
                </b-dropdown>

                <b-dropdown
                    id="ddown1"
                    text="Select by Ranking"
                    variant="outline-dark"
                    class="mr-1 float-md-left btn-group"
                    size="xs"
                >
                <b-dropdown-item
                    v-for="(rank,index) in rankOptions"
                    :key="index"
                    @click="changeOrderBy(rank, 'ranking')"
                >{{ rank }}</b-dropdown-item>
                </b-dropdown>
            </div>
        </b-collapse>
      </div>
      <div class="separator mb-5" />
    </b-colxx>
  </b-row>
</template>
<script>
import {
  DataListIcon,
  ThumbListIcon,
  ImageListIcon
} from "../../components/Svg"

import { mapGetters } from 'vuex'

export default {
    components: {
        "data-list-icon": DataListIcon,
        "thumb-list-icon": ThumbListIcon,
        "image-list-icon": ImageListIcon,
    },
    props: [
        "title",
        "displayMode",
        "changeDisplayMode",
        'categoryData',
        'entryData',
        'rankOptions',
        'tabIndex',
        'changeOrderBy'
    ],
    computed: {
        ...mapGetters({
            user: 'auth/user'
        }),
    },
};
</script>
