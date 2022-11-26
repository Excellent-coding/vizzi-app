<template>
<div>
    <b-row>
        <b-colxx xxs="12" class="mb-4">
            <home-header
                :timezone="timezone"
                :start="start"
                :end="end"
            />
        </b-colxx>
    </b-row>
    <b-row>
        <b-colxx md="12" lg="8" class="mb-4">
            <home-status 
                :users="users"
            />
            <home-map-status v-if="!sessionInProgress" />

              <video-player v-if="sessionInProgress" 
                class="video-player-box"
                             ref="videoPlayer"
                             :options="playerOptions"
                             :playsinline="true"
                             customEventName="customstatechangedeventname"

                             @play="onPlayerPlay($event)"
                             @pause="onPlayerPause($event)"
                             @ended="onPlayerEnded($event)"
                             @waiting="onPlayerWaiting($event)"
                             @playing="onPlayerPlaying($event)"
                             @loadeddata="onPlayerLoadeddata($event)"
                             @timeupdate="onPlayerTimeupdate($event)"
                             @canplay="onPlayerCanplay($event)"
                             @canplaythrough="onPlayerCanplaythrough($event)"

                             @statechanged="playerStateChanged($event)"
                             @ready="playerReadied">
              </video-player>
        </b-colxx>
        <b-colxx md="12" lg="4" class="mb-4">
            <home-information
                :basicData="basicData"
                :sessionData="sessionData"
            />
        </b-colxx>
    </b-row>
</div>
</template>

<script>
import { mapGetters } from 'vuex'
import Axios from 'axios'

import HomeHeader from '../../containers/Dashboard/HomeHeader'
import HomeStatus from '../../containers/Dashboard/HomeStatus'
import HomeMapStatus from '../../containers/Dashboard/HomeMapStatus'
import HomeInformation from '../../containers/Dashboard/HomeInformation'


import 'video.js/dist/video-js.css'

import videojs from 'video.js'
import { videoPlayer } from 'vue-video-player'
import dashPlugin from 'videojs-contrib-dash'

videojs.registerPlugin('examplePlugin', dashPlugin)

export default {
    components: {
        HomeHeader, HomeStatus, HomeMapStatus, HomeInformation, videoPlayer
    },
    data() {
        return {
            basicData: [
                {title: 'Ticket Sales', value: '0'},
                {title: 'Booths', value: '0 Active Sessions'},
                {title: 'Posters', value: '0 Active Sessions'},
                {title: 'Sponsors', value: '0 Active Sessions'}
            ],
            sessionData: [
                {title: 'General Session', value: 0},
                {title: 'Breakout Room', value: 600},
                {title: 'Networking Day 1', value: 742},
                {title: 'Networking Day 2', value: 564}
            ],
            users: 0,
            timezone: '',
            sessionInProgress: true,
            start: null,
            end: null,
            playerOptions: {
              // videojs options
              sources: [{
                src: "http://3.135.178.90:1935/vizzi/myStream/manifest.mpd",
                type: 'application/dash+xml'
              }],
            },
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId'
        }),

      player() {
        return this.$refs.videoPlayer.player
      }
    },
    mounted() {
        this.getInfo()
    },
    methods: {
        async getInfo() {
            const res = await Axios.post('/dashboard/info', {id: this.siteId})
            if (res.status == 200) {
                this.basicData[1].value = res.data.booth + ' Active Sessions'
                this.basicData[2].value = res.data.poster + ' Active Sessions'
                this.basicData[3].value = res.data.sponsor + ' Active Sessions'
                this.users = res.data.users
                this.timezone = res.data.timezone
                this.sessionData[0].value = res.data.sessionCount
                this.start = res.data.start
                this.end = res.data.end
            }
        },
      // listen event
      onPlayerPlay(player) {
      },
      onPlayerPause(player) {
      },
      // ...player event

      // or listen state event
      playerStateChanged(playerCurrentState) {
      },

      // player is ready
      playerReadied(player) {
        // you can use it to do something...
        // player.[methods]
      }
    }
}
</script>