<template>
  <div v-if="isShow" id="app-container" :class="menuType">
    <b-header
      :logo="logo"
      :headItems="headItems"
      :headerStyle="headerStyle"
      :modalItems="modalItems"
      :title="title"
      :siteStatus="siteStatus"
      @lobby-show="lobbyOpen"
    />
    <!-- <b-sitebar :height="height" /> -->
    <div :style="childStyle()" v-if="(user && user.role < 3) || siteStatus">
      <child
        @cart-open="cartOpen"
        @agenda-open="agendaOpen"
        @lobby-open="lobbyOpen"
        @agenda-filter-open="agendaFilterOpen"
      />
    </div>

    <agenda-modal
      :color="headerStyle.bg"
      :filterData="filterData"
      :filter="filter"
      :setValue="setValue"
      :setStatus="setStatus"
      :items="items"
      :agendaType="agendaType"
      :userData="userData"
      :adminData="adminData"
      :isDetail="isDetail"
      :agendaOpenTrue="agendaOpenTrue"
      :agendaFilter="agendaFilter"
      :filterValue="filterValue"
      @agenda-opened="agendaOpened"
      @is-detail="isDetailTrue"
      @agenda-close="agendaClose"
    />

    <lobby-modal
      :color="headerStyle.bg"
      :filterData="filterData"
      :filter="lobbyFilter"
      :setValue="setLobbyValue"
      :setStatus="setLobbyStatus"
      :items="lobbyItems"
      :agendaType="agendaType"
      :userData="userData"
      :adminData="adminData"
      :isDetail="isDetail"
      :lobbyOpenTrue="lobbyOpenTrue"
      @lobby-opened="lobbyOpened"
      @is-detail="isDetailTrue"
    ></lobby-modal>

    <b-modal
      ref="cart_modal"
      id="cart_modal"
      size="lg"
      centered
      hide-footer
      class="modal-class"
    >
      <template v-slot:modal-header="{ close }">
        <h5 class="text-capitalize">{{ $ct('cart') }} Items</h5>
        <b-button
          variant="primary"
          size="sm"
          class="float-right"
          @click="isEmail = true"
          v-if="getCartItems().length"
          >Email Send</b-button
        >
        <i
          class="close-btn simple-icon-close"
          style="right: -14px;top: -14px"
          @click="close()"
        ></i>
      </template>
      <template v-if="!isEmail">
        <template v-if="getCartItems().length">
          <div v-for="media in getCartItems()" :key="media.key">
            <div class="d-flex justify-content-between align-items-center">
              <div class="position-relative mr-3">
                <div
                  class="position-absolute front-type-text"
                  :style="getStyle(media.item)"
                >
                  {{ getTypeText(media.item) }}
                </div>
                <img :src="getType(media.item)" style="width: 48px" />
              </div>
              <h6 class="mr-auto my-auto text-capitalize">{{ media.title }}</h6>
              <b-button variant="outline-primary" size="sm" @click="view(media)"
                >View</b-button
              >
              <b-button
                variant="primary"
                size="sm"
                @click="addCartItem(media)"
                class="ml-2"
              >
                {{ getMark(media) ? '-' : '+' }} {{ $ct('cart') }}
              </b-button>
            </div>
            <hr />
          </div>
        </template>
        <div class="h5 text-center mb-5 mt-4 text-danger" v-else>
          You currently don't have any items added!<br />
          Please add items to save in your swag bag.
        </div>
      </template>
      <template v-else>
        <b-form-group class="mb-2">
          <b-form-textarea v-model="emailText" style="height: 120px" />
        </b-form-group>
        <b-button
          variant="outline-primary"
          size="sm"
          @click="emailSend"
          class="float-right"
          >Send</b-button
        >
        <b-button variant="primary" size="sm" @click="isEmail = false"
          >Return</b-button
        >
      </template>
    </b-modal>

    <b-modal
      ref="view_modal"
      id="view_modal"
      size="xl"
      centered
      hide-footer
      class="text-center"
    >
      <template v-slot:modal-header="{ close }">
        <h5 v-if="viewItem.title" class="text-capitalize">
          {{ viewItem.title }}
        </h5>
        <h5 v-else>{{ exhibitItem.title }}</h5>
        <i
          class="close-btn simple-icon-close"
          @click="close(), (isEmail = false)"
        ></i>
      </template>
      <template v-if="viewItem.item && viewItem.item.substring(0, 4) == 'http'">
        <video
          v-if="
            viewItem.item.includes('youtube') || viewItem.item.includes('vimeo')
          "
          width="100%"
          height="auto"
          autoplay="autoplay"
          loop="loop"
          muted=""
        >
          <source :src="viewItem.item" type="video/mp4" />
        </video>

        <iframe
          class="w-100"
          :src="viewItem.item"
          v-else-if="fileExtension === 'pdf'"
          style="height: 75vh"
        />

        <img class="w-100" :src="viewItem.item" v-else />
      </template>
      <template v-else>
        <video
          v-if="fileExtension === 'mp4'"
          width="100%"
          height="auto"
          autoplay="autoplay"
          loop="loop"
          muted=""
        >
          <source :src="'/data/' + viewItem.item" type="video/mp4" />
        </video>

        <iframe
          class="w-100"
          :src="'/data/' + viewItem.item"
          v-else-if="fileExtension === 'pdf'"
          style="height: 75vh"
        />

        <img class="w-100" :src="'/data/' + viewItem.item" v-else />
      </template>
    </b-modal>
    <!--<alert /> -->
    <notifications group="notifiers" />
  </div>
</template>

<script>
import Sitebar from '../containers/navs/Sitebar';
import SiteHeader from '../containers/Web/SiteHeader';
import Axios from 'axios';
import mixins from '../mixins/mixins.js';
import LobbyModal from '../containers/Web/LobbyModal';
import AgendaModal from '../containers/session/AgendaModal';
import { DataListIcon, ThumbListIcon } from '../components/Svg';

import { mapGetters, mapActions } from 'vuex';
import Cookies from 'js-cookie';
import PubNubVue from 'pubnub-vue';
//import Alert from '../containers/navs/Alert'
import moment from 'moment-timezone';

export default {
  components: {
    'b-sitebar': Sitebar,
    'b-header': SiteHeader,
    DataListIcon,
    ThumbListIcon,
    AgendaModal,
    LobbyModal,
    //alert: Alert
  },
  metaInfo() {
    return { title: `Vizzi` };
  },
  mixins: [mixins],
  data() {
    return {
      notifications: [],
      domain: Cookies.get('domain'),
      isShow: false,
      height: 32,
      logo: null,
      headItems: [],
      headerStyle: {
        bg: '#922c88',
        height: 32,
        btn: {
          bg: '#d683ce',
          color: '#ffffff',
          radius: 4,
          space: 8,
          boxShadow: true,
          hasBg: true,
          fontSize: 13,
          paddingX: 2,
          paddingY: 8,
        },
      },
      modalItems: [],
      title: '',
      agendaType: 0,
      sessionItems: [],
      sessionFilterItems: [],
      sessionDateData: [],
      sessionDate: -1,
      dateData: [],
      pageData: [],
      userData: [],
      adminData: {},
      viewItem: {},
      fileExtension: '',
      emailText: '',
      isEmail: false,
      filter: {
        value: 0,
        status: '',
      },
      lobbyFilter: {
        value: 0,
        status: '',
      },
      filterData: [],
      items: [],
      lobbyItems: [],
      isDetail: false,
      agendaOpenTrue: false,
      lobbyOpenTrue: false,
      agendaFilter: false,
      filterValue: 0,
    };
  },
  computed: {
    ...mapGetters({
      menuType: 'menu/getMenuType',
      user: 'auth/user',
      diffZone: 'auth/diffZone',
      startTime: 'site/getStartTime',
      endTime: 'site/getEndTime',
    }),
    siteStatus() {
		var moment = require('moment')
      var now = moment.tz('America/Denver')
      var start = moment.tz(this.startTime, 'America/Denver')
      var end = moment.tz(this.endTime, 'America/Denver')
      if (now.isBetween(start, end)) {
        return true
      } else {
        return false
      }
    }
  },
  methods: {
    ...mapActions({
      saveStyle: 'site/saveStyle',
    }),
    childStyle() {
      // return {
      //   'margin-left': '120px'
      // }
    },

    getNotifications: function() {
      var readTimeToken = localStorage.getItem('lastToken');
      var app = this;

      if (readTimeToken != null) {
        readTimeToken = parseInt(readTimeToken);
      } else {
        readTimeToken = 0;
      }

      this.$pnSubscribe({
        channels: [this.domain],
      });

      PubNubVue.getInstance()
        .history({
          //end: readTimeToken + 2,
          channel: [this.domain],
          //count: 3, // how many items to fetch
          stringifiedTimeToken: true, // false is the default
        })
        .then(response => {
            this.notifications = response.messages

            response.messages.forEach(message => {
                setTimeout(() => {
                  app.$notify({
                    group: 'notifiers',
                    //title: 'Important message',
                    duration: 20000,
                    speed: 1000,
                    type: 'success',
                    text: message.entry
                  });
                  if (readTimeToken < parseInt(message.timetoken)){
                      localStorage.setItem('lastToken', parseInt(message.timetoken))
                      readTimeToken = message.timetoken
                  }
                }, 100);
            })
            
        }).catch((err) => {
            console.log(err)
        })
        .catch(err => {
          console.log(error);
        });
    },

    setValue(val) {
      this.filter.value = val;
      this.filter.status = '';
      this.isDetail = false;
    },
    setLobbyValue(val) {
      this.lobbyFilter.value = val;
      this.lobbyFilter.status = '';
      this.isDetail = false;
    },
    isDetailTrue() {
      this.isDetail = true;
    },
    agendaClose() {
      this.filter = {
        value: 0,
        status: '',
      };
    },
    setStatus(val) {
      this.filter.status = val;
    },
    setLobbyStatus(val) {
      this.lobbyFilter.status = val;
    },
    chooseTrack(track) {
      this.sessionFilterItems = [];
      if (this.sessionItems && this.sessionItems.length)
        this.sessionItems.forEach(item => {
          if (item.track.id == track.id) {
            this.sessionFilterItems.push(item);
          }
        });
      this.agendaType = 1;
    },

    getTitle(item) {
      var title = '';
      if (this.pageData && this.pageData.length)
        this.pageData.forEach(data => {
          if (data.url == item) {
            title = data.title;
          }
        });
      return title;
    },
    getStyle(file) {
      const extension = file.split('.').pop();
      if (extension == 'pdf') {
        return {
          'letter-spacing': '4px',
          background: this.headerStyle.bg,
        };
      } else {
        return {
          'letter-spacing': '1px',
          background: this.headerStyle.bg,
        };
      }
    },
    getTypeText(file) {
      const extension = file.split('.').pop();
      if (extension == 'mp4') {
        return 'video';
      } else if (extension == 'pdf') {
        return 'pdf';
      } else {
        return 'image';
      }
    },
    getType(file) {
      const extension = file.split('.').pop();
      if (extension == 'mp4') {
        return '/assets/img/type/video.png';
      } else if (extension == 'pdf') {
        return '/assets/img/type/pdf.png';
      } else {
        return '/assets/img/type/image.png';
      }
    },
    view(item) {
      this.viewItem = item;
      this.fileExtension = this.viewItem.item.split('.').pop();
      this.$refs.view_modal.show();
    },
    emailSend() {
      Axios.post('/site/email-send', {
        items: this.getCartItems(),
        text: this.emailText,
      }).then(res => {
        if (res.status == 200) {
          this.$notify('primary filled', '', 'Successfully Sended!', {
            duration: 3000,
            permanent: false,
          });
        } else {
          this.$notify(
            'primary filled',
            'Server Error!',
            'Please try a few hours later!',
            { duration: 3000, permanent: false }
          );
        }
        this.isEmail = false;
        this.$refs.cart_modal.hide();
      });
    },
    getFilterStyle() {
      if (this.isHover == true) {
        return {
          background: this.headerStyle.bg,
        };
      } else {
        return {
          background: this.headerStyle.bg,
        };
      }
    },
    cartOpen() {
      this.$refs.cart_modal.show();
    },
    agendaOpen() {
      this.agendaFilter = false;
      this.agendaOpenTrue = true;
    },
    agendaFilterOpen(val) {
      this.filter = {
        value: 5,
        status: val,
      };
      this.agendaFilter = true;
      this.filterValue = val;
      this.agendaOpenTrue = true;
    },
    agendaOpened() {
      this.agendaOpenTrue = false;
    },
    lobbyOpen() {
      this.lobbyOpenTrue = true;
    },
    lobbyOpened() {
      this.lobbyOpenTrue = false;
    },
    getSiteData() {
      axios.post('/site/head', {domain: this.host}).then(res => {
        this.isShow = true
        this.headItems = res.data.headItems
        this.modalItems = res.data.modalItems
        this.videoItems = res.data.videoItems
        this.lobbyItems = this.videoItems
        this.logo = res.data.logo
        this.title = res.data.title
        if (res.data.style) {
          this.height = JSON.parse(res.data.style).height
          this.headerStyle = JSON.parse(res.data.style)
        }
        this.saveStyle({style: this.headerStyle})
        this.sessionItems = res.data.sessionItems
        this.items = this.sessionItems
        this.filterData[2] = res.data.sessionDateData
        this.filterData[1] = res.data.trackData
        this.pageData = res.data.pageData
        this.userData = res.data.domain.user
        this.adminData = res.data.domain.admin
        this.filter_items();
      }).catch(function (e) {
      })
      setTimeout(() => {
        document.body.classList.add('default-transition');
        document.body.classList.add('rounded');
      }, 100);

      setTimeout(() => {
        this.getSiteData();
      }, 25 * 1000) // seconds refresh data here!!!! findme
    },
    filter_items() {
		var moment = require('moment')
        var sessions = []
		this.agendaType = 0
        switch (this.filter.value) {
          case 0:
			let currentSessions = []
			let endedSessions = []
			let futureSessions = []
			this.sessionItems.forEach(element => {
				var currentTime = moment.tz('America/Denver')
				var endTime = moment.tz(element.event.date + ' ' + element.end, 'America/Denver')
				var startTime = moment.tz(element.event.date + ' ' + element.start, 'America/Denver')

				if(currentTime.isBetween(startTime, endTime)) {
					currentSessions.push(element)
				} else if(currentTime.isBefore(startTime)) {
					futureSessions.push(element)
				} else if(currentTime.isAfter(endTime)) {
					endedSessions.push(element)
				}
			})
			sessions = [
				...currentSessions,
				...futureSessions,
				...endedSessions
			]
			break
          case 1:
            if (this.filter.status == '') {
              sessions = this.sessionItems;
            } else {
              this.sessionItems.forEach(element => {
                if (element.track_id == this.filter.status) {
                  sessions.push(element);
                }
              });
            }
            this.agendaType = 1;
            break;
          case 2:
            if (this.filter.status == '') {
              sessions = this.sessionItems;
            } else {
				let currentSessions = []
				let endedSessions = []
				let futureSessions = []
				this.sessionItems.forEach(element => {
					if (element.event_id == this.filter.status) {
						if(element.start && element.end) {
							var currentTime = moment.tz('America/Denver')
							var endTime = moment.tz(element.event.date + ' ' + element.end, 'America/Denver')
							var startTime = moment.tz(element.event.date + ' ' + element.start, 'America/Denver')

							if(currentTime.isBetween(startTime, endTime)) {
								currentSessions.push(element)
							} else if(currentTime.isBefore(startTime)) {
								futureSessions.push(element)
							} else if(currentTime.isAfter(endTime)) {
								endedSessions.push(element)
							}
						} else {
							futureSessions.push(element)
						}
					}
				})

				sessions = [
					...currentSessions,
					...futureSessions,
					...endedSessions
				]
            }
            break;
          case 3:
            this.agendaType = 1;
            if (this.filter.status == '') {
              sessions = this.sessionItems;
            } else {
              var users = [];
              this.userData.forEach(user => {
                if (
                  user.name
                    .toLowerCase()
                    .includes(this.filter.status.toLowerCase())
                ) {
                  users.push(user);
                }
              });
              if (
                this.adminData.name
                  .toLowerCase()
                  .includes(this.filter.status.toLowerCase())
              ) {
                users.push(this.adminData);
              }
              this.sessionItems.forEach(item => {
                users.forEach(user => {
                  if (item.presenter) {
                    JSON.parse(item.presenter).forEach(element => {
                      if (element == user.email) {
                        sessions.push(item);
                      }
                    });
                  }
                });
              });
            }
            break;
          case 4:
            var currentTime = moment.tz('America/Denver')
            this.sessionItems.forEach(item => {
              var endTime = moment(item.event.date + ' ' + item.end)
			  var startTime = moment.tz(item.event.date + ' ' + item.start, 'America/Denver')

				if(currentTime.isBetween(startTime, endTime)) {
					currentSessions.push(item)
				} else if(currentTime.isBefore(startTime)) {
					futureSessions.push(item)
				} else if(currentTime.isAfter(endTime)) {
					endedSessions.push(item)
				}
            })
            break
          case 5:
            this.sessionItems.forEach(item => {
              if (
                this.filter.status ==
                new Date(
                  Date.parse(item.event.date + ' ' + item.start)
                ).getDay()
              ) {
                sessions.push(item);
              }
            });
        }
		this.items = sessions
      }
  },
  mounted() {
    this.getSiteData();
    let app = this;

    setTimeout(() => {
      this.getNotifications() ;
    }, 2000);

    

    setTimeout(() => {
        document.body.classList.add("default-transition");
        document.body.classList.add("rounded");

        setTimeout(() => {
          app.getSiteData();
        }, 25 * 1000) // seconds refresh data here!!!! findme

      }, 100)
  },
  watch: {
    filter: {
      deep: true,
      handler() {
        this.filter_items()
      }
    },
    lobbyFilter: {
      deep: true,
      handler() {
        var sessions = [];
        this.agendaType = 0;
        switch (this.lobbyFilter.value) {
          case 0:
            sessions = this.videoItems;
            break;
          case 1:
            if (this.lobbyFilter.status == '') {
              sessions = this.videoItems;
            } else {
              this.videoItems.forEach(element => {
                if (element.track_id == this.lobbyFilter.status) {
                  sessions.push(element);
                }
              });
            }
            this.agendaType = 1;
            break;
          case 3:
            this.agendaType = 1;
            if (this.lobbyFilter.status == '') {
              sessions = this.videoItems;
            } else {
              var users = [];
              this.userData.forEach(user => {
                if (
                  user.name
                    .toLowerCase()
                    .includes(this.lobbyFilter.status.toLowerCase())
                ) {
                  users.push(user);
                }
              });
              if (
                this.adminData.name
                  .toLowerCase()
                  .includes(this.lobbyFilter.status.toLowerCase())
              ) {
                users.push(this.adminData);
              }
              this.videoItems.forEach(item => {
                users.forEach(user => {
                  if (item.presenter) {
                    JSON.parse(item.presenter).forEach(element => {
                      if (element == user.email) {
                        sessions.push(item);
                      }
                    });
                  }
                });
              });
            }
            break;
          case 4:
            var currentTime = Math.round(new Date().getTime());
            this.videoItems.forEach(item => {
			  var endTime = Date.parse(item.date + ' ' + item.end)
              if (currentTime > endTime) {
                sessions.push(item);
              }
            });
            break;
        }
        this.lobbyItems = sessions;
      },
    },
    sessionDate(val) {
      if (val > -1) {
        this.sessionFilterItems = this.sessionDateData[val].session;
        this.agendaType = 1;
      } else {
        this.agendaType = 0;
      }
    },
  },
}
</script>

<style>
.dropdown-menu {
  margin-top: 0 !important;
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 1000;
  display: none;
  float: left;
  min-width: 10rem;
  padding: 0.5rem 0 !important;
  font-size: 0.8rem;
  color: #212529;
  text-align: left !important;
  list-style: none;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid rgba(0, 0, 0, 0.15);
  border-radius: 0.75rem;
}
.header {
  min-width: 40px;
}
.header-btn {
  font-weight: 400;
  min-width: 64px;
  transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,
    box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,
    border 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
  font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;
  line-height: 1.75;
  letter-spacing: 0.02857em;
  text-transform: capitalize;
  justify-content: center;
  cursor: pointer;
}
.title {
  font-size: 1.2rem;
}
.logo {
  height: 64px;
  object-fit: contain;
  width: 32px;
  padding: 2px;
}
.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
}
.chat-box ::-webkit-scrollbar {
  width: 6px;
}
.chat-box ::-webkit-scrollbar-track {
  border-radius: 3px;
  background: transparent;
}
.chat-box ::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.6);
  border-radius: 3px;
}
.chat-box ::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.8);
}
.chat-box {
  position: fixed;
  right: 2%;
  bottom: 16%;
  left: auto;
  z-index: 99;
}
.chat-box i {
  cursor: pointer;
}
.chat-box .chat-form {
  background: rgba(0, 0, 0, 0.6);
  color: white;
  border-radius: 8px;
  padding: 12px;
  width: 270px;
  height: 50vh;
  font-size: 14px;
}
.chat-box .chat-container {
  width: 100%;
  height: calc(50vh - 106px);
  background: rgba(255, 255, 255, 0.1);
  padding: 8px;
  border-radius: 8px;
}
.chat-box .chat-container .message {
  width: fit-content;
  max-width: 75%;
}
.chat-box .chat-container .message.sended .chat-content {
  background: #e33bab;
  border-radius: 8px;
  padding: 8px;
}
.chat-box .chat-container .message .chat-content {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  padding: 8px;
}
.chat-box .chat-input i {
  background: #e33bab;
  border-radius: 8px;
  margin-left: 8px;
}
.chat-box .chat-time {
  font-size: 8px;
}
.chat-box .chat-input input {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  border-radius: 8px;
  width: calc(100% - 30px);
  color: white;
}
.chat-box .chat-message {
  overflow: auto;
  height: calc(50vh - 154px);
}
.chat-box .chat-icon {
  padding: 20px;
  font-size: 20px;
  background: #e33bab;
  color: white;
  border-radius: 50%;
  cursor: pointer;
}
.chat-box .chat-icon:hover {
  box-shadow: 0 0 10px 0 grey;
}
.chat-box .init-form {
  background: rgba(0, 0, 0, 0.6);
  color: white;
  border-radius: 8px;
  padding: 12px;
  text-align: center;
  width: 270px;
  height: 360px;
  font-size: 14px;
}
.chat-box .init-form .des {
  font-size: 14px;
  padding: 0 20px;
}
.chat-box .init-form .chat-box-icon {
  background: #e33bab;
  color: white;
  border-radius: 50%;
  padding: 12px;
  font-size: 20px;
}
.chat-box .status {
  background: chartreuse;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  position: absolute;
  bottom: -4px;
  right: -4px;
  left: auto;
}
.chat-box .chat-avatar {
  border-radius: 4px;
  width: 48px;
  height: 48px;
}
.chat-box .chat-avatar-sm {
  border-radius: 4px;
  width: 36px;
  height: 36px;
}
.chat-box .chat-user-name {
  font-size: 12px;
}
.chat-box .init-form .chat-conversation {
  background: #0027f5;
  color: white;
  border-radius: 4px;
  padding: 4px 20px;
  transform: rotate(0);
}
.chat-box .init-form .chat-conversation:hover {
  box-shadow: 0px 0px 5px 0px rgba(255, 255, 255, 0.4) !important;
  transform: translateY(-2px);
}
.chat-box .chat-bg {
  left: 16px;
  bottom: 16px;
  right: 16px;
  width: 238px;
}
#agenda_modal___BV_modal_content_ {
  border-radius: 1rem;
  position: relative;
  border: 0;
}
.close-btn {
  font-size: 25px;
  border-radius: 50%;
  background: #212121;
  color: white;
  box-shadow: 0px 0px 1px 1px black;
  position: absolute;
  right: -28px;
  top: -28px;
  cursor: pointer;
}
#agenda_modal___BV_modal_content_ .modal-header {
  padding: 0;
}
#agenda_modal___BV_modal_content_ .close-btn {
  font-size: 25px;
  border-radius: 50%;
  background: #212121;
  color: white;
  box-shadow: 0px 0px 1px 1px black;
  position: absolute;
  right: -6px;
  top: -6px;
  cursor: pointer;
}
.agenda-body {
  padding: 0;
  max-height: 60vh;
  overflow: auto;
  border-radius: 1rem;
}
#agenda_modal___BV_modal_body_ {
  padding: 0;
}
#agenda_modal___BV_modal_body_ .p-4 {
  cursor: pointer;
}
#agenda_modal___BV_modal_body_ .p-4:nth-child(odd) {
  background: #f4f6f8;
}
#agenda_modal___BV_modal_body_ .p-4:hover {
  background: #f2f2f2;
}
.agenda-body::-webkit-scrollbar {
  width: 8px;
}
.agenda-body::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 4px;
}
.agenda-body::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.4);
}
.icon-btn {
  font-size: 1.2rem;
  color: black;
  border: 1px solid grey;
  border-radius: 6px;
}
.icon-btn:hover {
  background: #922688;
  color: white;
}
.h-b-tooltip {
  z-index: 99999 !important;
}
#agenda_modal___BV_modal_body_ {
  margin-top: -1px;
  border-radius: 0;
}
.front-type-text {
  padding: 1px 5px;
  top: 14px;
  font-size: 8px;
  border-radius: 0 40px 40px 0;
  text-transform: uppercase;
  font-weight: bold;
  color: white;
}
.modal-dialog #agenda_modal___BV_modal_content_.modal-content {
  border: none !important;
}
#ddwn1__BV_toggle_:hover,
#ddwn2__BV_toggle_:hover {
  background: var(--color) !important;
  border-radius: 50px;
  border-color: var(--color) !important;
}
.show > .btn-outline-dark.dropdown-toggle {
  background: var(--color) !important;
  border-color: var(--color) !important;
}

.vue-notification {
  padding: 10px;
  margin: 0 5px 5px;

  font-size: 12px;

  color: #ffffff;
  background: #44a4fc;
  border-left: 5px solid #187fe7;
}
.vue-notification .warn {
  background: #ffb648;
  border-left-color: #f48a06;
}

.vue-notification .error {
  background: #e54d42;
  border-left-color: #b82e24;
}

.vue-notification .success {
  background: #68cd86;
  border-left-color: #42a85f;
}

.card-header {
  margin: 20px 0px;
  font-size: 20px;
  font-weight: bold;
}
</style>
