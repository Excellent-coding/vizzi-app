<template>
<b-card :class="{'d-flex flex-row':true,'active' : selectedUsers.includes(user.id)}" no-body>
    <div class="custom-control custom-checkbox pl-1 align-self-center pr-4" @click.prevent="toggleItem($event,user.id)" >
        <b-form-checkbox :checked="selectedUsers.includes(user.id)" class="itemCheck mb-0" />
    </div>
    <img :src="'/assets/img/avatar/' + user.avatar" class="list-thumbnail responsive border-0 my-auto" :alt="user.name" />
    <div class="pl-2 d-flex flex-grow-1 min-width-zero">
        <div class="w-100 align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
            <router-link :to="`?p=${user.id}`" class="w-20 w-sm-100">
                <h6>{{user.name}}</h6>
                <p class="list-item-heading mb-0 truncate">{{user.email}}</p>
            </router-link>
            <p class="mb-0 text-muted text-small w-20 w-sm-100">{{user.created_at | moment('MMMM Do YYYY')}}</p>
            <div class="w-15 w-sm-100">
                <b-badge pill variant="primary" v-if="user.status">Active</b-badge>
                <b-badge pill variant="danger" v-else>Inactive</b-badge>
            </div>
            <div class="w-15 w-sm-100" v-if="role == 0">
                <b-badge pill variant="primary" v-if="user.role == 2">Admin</b-badge>
                <b-badge pill variant="info" v-else-if="user.role == 3 && user.type == 0">Booth Manager</b-badge>
                <b-badge pill variant="info" v-else-if="user.role == 3 && user.type == 1">Sponsor Manager</b-badge>
                <b-badge pill variant="info" v-else-if="user.role == 3 && user.type == 2">Poster Manager</b-badge>
                <b-badge pill variant="secondary" v-else-if="user.role == 5">Front User</b-badge>
                <b-badge pill variant="dark" v-else-if="user.role == 6">Event Stuff</b-badge>
            </div>
            <!-- <b-form-group :label="$t('verify-code')" class="w-45 w-sm-100 text-muted text-small">
                <b-input-group>
                    <b-input-group-prepend @click="generate()">
                    <b-input-group-text class="text-muted text-small">Generate</b-input-group-text>
                    </b-input-group-prepend>
                    <b-form-input v-model="user.verify_code" />
                </b-input-group>
            </b-form-group> -->
        </div>
        <b-button
            v-b-modal.user_modal
            variant="outline-primary" 
            class="my-auto mr-2"
            @click="edit(user)"
        >Edit</b-button>
    </div>
</b-card>
</template>

<script>
export default {
    props: ['user', 'selectedUsers', 'edit', 'role'],
    methods: {
        toggleItem(event, itemId) {
            this.$emit('toggle-item', event, itemId)
        },
        generate() {
            this.user.verify_code = Math.round(Math.random() * 1000000)
        }
    }
}
</script>
