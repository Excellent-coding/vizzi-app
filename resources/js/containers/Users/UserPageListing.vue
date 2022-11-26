<template>
    <div>
        <b-row v-if="displayMode == 'thumb'">
            <b-colxx xxs="12" class="mb-3" v-for="(user, index) in users" :key="index" :id="user.id">
                <user-thumb-view
                    :key="user.id"
                    :user="user"
                    :selectedUsers="selectedUsers"
                    :edit="edit"
                    :role="role"
                    @toggle-item="toggleItem"
                />
            </b-colxx>
        </b-row>
        <b-row v-else>
            <b-colxx xxs="12" class="mb-3" v-for="(user, index) in users" :key="index" :id="user.id">
                <user-list-view
                    :key="user.id"
                    :user="user"
                    :selectedUsers="selectedUsers"
                    :edit="edit"
                    :role="role"
                    @toggle-item="toggleItem"
                />
            </b-colxx>
        </b-row>
        <b-row v-if="lastPage>1">
            <b-colxx xxs="12">
                <b-pagination-nav
                :number-of-pages="lastPage"
                :link-gen="linkGen"
                :value="page"
                @change="(a)=>changePage(a)"
                :per-page="perPage"
                align="center"
                >
                <template v-slot:next-text>
                    <i class="simple-icon-arrow-right" />
                </template>
                <template v-slot:prev-text>
                    <i class="simple-icon-arrow-left" />
                </template>
                <template v-slot:first-text>
                    <i class="simple-icon-control-start" />
                </template>
                <template v-slot:last-text>
                    <i class="simple-icon-control-end" />
                </template>
                </b-pagination-nav>
            </b-colxx>
        </b-row>
    </div>
</template>

<script>
import UserListView from '../../components/Pages/UserListView'
import UserThumbView from '../../components/Pages/UserThumbView'

export default {
    components: {
        UserListView, UserThumbView
    },
    props: [
        'displayMode',
        'users',
        'selectedUsers',
        'toggleItem',
        'edit',
        "lastPage",
        "perPage",
        "page",
        "changePage",
        "role"
    ],
    methods: {
        linkGen(pageNum) {
            return "#";
        }
    }
}
</script>