<template>
    <b-modal ref="lobby_modal" id="lobby_modal" size="lg" centered hide-footer>
        <div slot="modal-header" :style="agendaHeader()">
            <i class="simple-icon-close position-absolute close-btn" :style="'background:' + color" @click="$refs['lobby_modal'].hide()"></i>
            <div class="d-flex justify-content-end align-items-center w-100">
                <h2 class="mr-auto">Videos</h2>
                <div class="d-block d-md-inline-block pt-1">
                    <filter-options
                        :statusOptions="statusOptions"
                        :styleObject="styleObject"
                        @set-value="setValue"
                    />
                    <filter-sub-options
                        v-if="filter.value > 0 && filter.value < 3"
                        :styleObject="styleObject"
                        :filterData="filterData[filter.value]"
                        :dataIndex="filter.value"
                        @set-status="setStatus"
                    />
                    <search-item @set-status="setStatus" v-if="filter.value == 3" />
                </div>
            </div>
        </div>
        <div class="agenda-body" :style="agendaBody()">
            <template v-if="agendaType == 0 && !isDetail">
                <div class="p-4 bg-light" v-for="item in items" :key="item.id" @click="agendaView(item)">
                    <div class="float-right">
                        <div class="d-flex">
                        <i class="iconsminds-checkout icon-btn mr-2"></i>
                        <i class="simple-icon-calendar icon-btn" style="padding: 4px"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-muted">{{item.date | moment('dddd, MMMM Do YYYY')}} {{item.date + ' ' + item.start | moment('h:mm A')}}-{{item.date + ' ' + item.end | moment('h:mm A')}}: {{item.track.title}}</p>
                        <h4>{{item.title}}</h4>
                        <div class="d-flex">
                            <div class="position-relative mx-1" v-for="user in selectedUsers(item)" :key="user.id">
                                <img :src="'/assets/img/avatar/' + user.avatar" class="user-avatar" alt=""  :key="user.id">
                                <span v-if="user.role == 2" :style="adminMark()" v-b-tooltip.hover title="Admin"/>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else-if="isDetail && agendaType == 0">
                <div class="p-4 m-2 bg-light" v-for="item in sessionFilterItems" :key="item.id" :style="getStatuBar(item)">
                    <div class="float-right">
                        <div class="d-flex">
                        <i class="iconsminds-checkout icon-btn mr-2"></i>
                        </div>
                    </div>
                    <div>
                        <p>{{item.date | moment('dddd, MMMM Do YYYY')}} {{item.date + ' ' + item.start | moment('h:mm A')}}-{{item.date + ' ' + item.end | moment('h:mm A')}}: {{item.track.title}}</p>
                        <h4>{{item.title}}</h4>
                        <h6>{{item.description}}</h6>
                        <div class="d-flex">
                            <div class="position-relative mx-1" v-for="user in selectedUsers(item)" :key="user.id">
                                <img :src="'/assets/img/avatar/' + user.avatar" class="user-avatar" alt=""  :key="user.id">
                                <span v-if="user.role == 2" :style="adminMark()" v-b-tooltip.hover title="Admin"/>
                            </div>
                        </div>
                    </div>
                    <b-button variant="primary" size="sm" class="float-right" style="margin-top: -24px; border-radius: 6px" :disabled="isDisable(item) == true" @click="$refs.agenda_modal.hide()">
                        <template v-if="isPast(item)">
                            Watch On Demand
                        </template>
                        <template v-else>
                            <template v-if="item.pageType == 'Video'">
                                <template v-if="isDisable(item)">
                                    Watch Live&nbsp;
                                    <i class="simple-icon-camrecorder"></i>
                                </template>
                                <router-link to="/session" class="text-white" v-else>
                                    Watch Live&nbsp;
                                    <i class="simple-icon-camrecorder"></i>
                                </router-link>
                            </template>
                            <template v-else>
                                Enter <span class="text-capitalize">{{item.title}}</span>
                            </template>
                        </template>
                    </b-button>
                </div>
            </template>
            <template v-else>
                <div class="p-4 m-2 bg-light" v-for="item in items" :key="item.id" :style="getStatuBar(item)">
                    <div class="float-right">
                        <div class="d-flex">
                        <i class="iconsminds-checkout icon-btn mr-2"></i>
                        </div>
                    </div>
                    <div>
                        <p>{{item.date | moment('dddd, MMMM Do YYYY')}} {{item.date + ' ' + item.start | moment('h:mm A')}}-{{item.date + ' ' + item.end | moment('h:mm A')}}: {{item.track.title}}</p>
                        <h4>{{item.title}}</h4>
                        <h6>{{item.description}}</h6>
                        <div class="d-flex">
                            <div class="position-relative mx-1" v-for="user in selectedUsers(item)" :key="user.id">
                                <img :src="'/assets/img/avatar/' + user.avatar" class="user-avatar" alt=""  :key="user.id">
                                <span v-if="user.role == 2" :style="adminMark()" v-b-tooltip.hover title="Admin"/>
                            </div>
                        </div>
                    </div>
                    <b-button variant="primary" size="sm" class="float-right" style="margin-top: -24px; border-radius: 6px" :disabled="isDisable(item) == true" @click="$refs.agenda_modal.hide()">
                        <template v-if="isPast(item)">
                            Watch On Demand
                        </template>
                        <template v-else>
                            <template v-if="item.pageType == 'Video'">
                                <template v-if="isDisable(item)">
                                    Watch Live&nbsp;
                                    <i class="simple-icon-camrecorder"></i>
                                </template>
                                <router-link to="/session" class="text-white" v-else>
                                    Watch Live&nbsp;
                                    <i class="simple-icon-camrecorder"></i>
                                </router-link>
                            </template>
                            <template v-else>
                                Enter <span class="text-capitalize">{{item.title}}</span>
                            </template>
                        </template>
                    </b-button>
                </div>
            </template>
        </div>
    </b-modal>
</template>

<script>
import TrackColorItem from '../../components/TrackColorItem'
import SearchItem from '../../components/SearchItem'
import FilterOptions from '../session/FilterOptions'
import FilterSubOptions from '../session/FilterSubOptions'

export default {
    props: ['color', 'setValue', 'filter', 'filterData', 'setStatus', 'items', 'agendaType', 'userData', 'is-detail', 'lobby-opened', 'isDetail', 'adminData', 'lobbyOpenTrue'],
    components: {
        TrackColorItem,
        SearchItem,
        FilterOptions,
        FilterSubOptions
    },
    computed: {
        styleObject() {
            return {
                '--color': this.color
            }
        }
    },
    data() {
        return {
            statusOptions: [
                {value: 0, text: 'All'},
                {value: 1, text: 'Track'},
                {value: 3, text: 'Name'},
                {value: 4, text: 'Previous Sessions'}
            ],
            filterSubText: '',
            sessionFilterItems: []
        }
    },
    watch: {
        lobbyOpenTrue(val) {
            // this.$refs.lobby_modal.show()
            // this.$emit('lobby-opened')
        }
    },
    methods: {
        agendaHeader() {
            return {
                'width': '100%',
                'height': '100%',
                'border': '8px solid ' + this.color,
                'border-bottom': 0,
                'border-radius': '16px 16px 0 0',
                'padding': '1.75rem'
            }
        },
        agendaBody() {
            return {
                'width': '100%',
                'height': '100%',
                'border': '8px solid ' + this.color,
                'border-top': 0,
                'border-radius': '0 0 16px 16px'
            }
        },
        agendaView(item) {
            if (this.agendaType == 0) {
                this.sessionFilterItems = []
                    if (this.items && this.items.length) this.items.forEach(session => {
                    if (session.date == item.date) {
                        this.sessionFilterItems.push(session)
                    }
                });
                this.$emit('is-detail')
            }
        },
        getStatuBar(item) {
            return {
                'border-left': '4px solid ' + item.track.color
            }
        },
        adminMark() {
            return {
                'position': 'absolute',
                'background': this.color,
                'width': '12px',
                'height': '12px',
                'box-shadow': '0 0 5px 0 ' +  this.color,
                'margin-left': '-10px',
                'border': '2px solid white',
                'border-radius': '50%'
            }
        },
        selectedUsers(item) {
            var selectedUsers = []
            var users = []
            if (item.presenter) {
                users = JSON.parse(item.presenter)
            }
            this.userData.forEach(element => {
                users.forEach(current => {
                    if (current == element.id && element.avatar != 'default.jpg') {
                        selectedUsers.push(element)
                    }
                })
            });
            users.forEach(current => {
                if (current == this.adminData.id && this.adminData.avatar != 'default.jpg') {
                    selectedUsers.push(this.adminData)
                }
            })
            return selectedUsers
        },
        isDisable(item) {
            // var currentTime = Math.round(new Date().getTime() / 1000)
            // var startTime = Date.parse(item.date + ' ' + item.start) / 1000
            // var start = startTime - item.button * 60
            // if (start > currentTime) {
            //     return true
            // } else {
            //     return false
            // }
        },
        isPast(item) {
            // var currentTime = Math.round(new Date().getTime() / 1000)
            // var endTime = Date.parse(item.date + ' ' + item.end) / 1000
            // if (endTime < currentTime) {
            //     return true
            // } else {
            //     return false
            // }
        }
    }
}
</script>