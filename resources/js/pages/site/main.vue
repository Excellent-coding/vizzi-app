<template>
  <div>
    <div id="root" v-if="isShow">
      <div class="w-100">
        <div class="header text-center w-100" :style="getHeaderStyle()">
          <div class="d-inline-flex">
            <template v-for="(item, index) in headItems">
              <div
                class="header-btn"
                :key="index"
                :style="getButtonStyle()"
                @click="getPage(item)"
              >
                {{ item.title }}
              </div>
            </template>
          </div>
        </div>
      </div>
      <div class="position-relative body-bg" v-if="type == 'page'">
        <template v-if="page.background">
          <template v-if="page.background.substr(0, 4).toLowerCase() == 'http'">
            <iframe
              :src="page.background + '?background=1'"
              style="width: 100%;height: 56vw"
              frameborder="0"
              webkitallowfullscreen
              mozallowfullscreen
              allowfullscreen
            ></iframe>
          </template>
          <template v-else>
            <video
              width="100%"
              height="auto"
              autoplay="autoplay"
              loop="loop"
              muted=""
              v-if="extension === 'mp4'"
            >
              <source :src="'/data/' + page.background" type="video/mp4" />
            </video>
            <div v-else-if="!extension"></div>
            <img :src="'/data/' + page.background" class="w-100" v-else />
          </template>
        </template>
        <template v-if="page.type == 'content'">
          <div
            :id="'event_' + index"
            v-for="(point, index) in points"
            :key="index"
            class="position-absolute area text-left"
            :style="pointStyle(point.position)"
            @click="getEvent(point)"
          >
            <img
              :src="'/data/' + media[index]"
              v-if="
                media[index] &&
                  media[index].substr(0, 4) != 'http' &&
                  point.link != 'Embed Video'
              "
              class="w-100 h-100"
              style="object-fit: cover"
            />
            <img
              :src="media[index]"
              v-if="
                media[index] &&
                  media[index].substr(0, 4) == 'http' &&
                  point.link != 'Embed Video'
              "
              class="w-100 h-100"
              style="object-fit: cover"
            />
            <div
              v-b-modal.video_modal
              class="w-100 h-100"
              style="object-fit: cover"
              v-if="point.link == 'Embed Video'"
              @click="welcomeVideo = point.media"
            ></div>
          </div>
        </template>
        <template v-else>
          <div class="container">
            <div
              class="exhibit-header text-center"
              :style="getExhibitHeaderStyle()"
            >
              <div class="d-inline-flex">
                <div
                  class="header-btn"
                  v-for="(item, index) in menus"
                  :key="index"
                  :style="getExhibitButtonStyle()"
                  @click="getHtml(item)"
                >
                  {{ item.title }}
                </div>
              </div>
            </div>
            <div v-html="exhibitHtml" style="min-height: 200px"></div>
          </div>
        </template>
        <chat-desk v-if="helpChat" :type="chatType" @chat-close="chatClose" />
      </div>
      <div class="body-bg" v-else-if="type == 'exhibit'">
        <b-colxx
          md="6"
          sm="12"
          lg="4"
          xxs="12"
          v-for="(item, index) in exhibitItems"
          :key="item.id"
        >
          <div class="w-100 my-3">
            <img
              :src="'/data/' + item.media"
              alt="Sponsor"
              class="w-100 cursor-pointer border-rounded"
              @click="viewExhibit(item, index)"
            />
          </div>
        </b-colxx>
      </div>
      <div class="body-bg position-relative" v-else-if="type == 'iframe'">
        <iframe
          class="w-100"
          :src="page.media"
          allowfullscreen
          style="border: 0;height: 800px"
        />
      </div>
      <div class="body-bg" v-else>
        <div class="w-100 position-relative">
          <div class="prevBooth" @click="backBooth">Back</div>
          <div class="nextBooth" @click="nextBooth">Next</div>
          <img
            :src="'/data/' + exhibitItem.media"
            alt="Sponsor"
            class="w-100"
            v-if="exhibitItem.type != 'poster'"
            id="content"
          />
          <div
            class="position-absolute"
            :style="itemStyle(data)"
            v-for="data in exhibitItemData"
            :key="data.id"
          >
            <img
              :src="'/data/' + data.media"
              class="w-100 h-100"
              style="object-fit: cover"
              v-if="data.media.substring(0, 4) != 'http'"
            />
            <img
              :src="data.media"
              class="w-100 h-100"
              style="object-fit: cover"
              v-else
            />
          </div>
          <div class="container">
            <b-row>
              <b-col
                class="exhibit-header text-center w-100"
                :style="
                  getExhibitHeaderStyle(JSON.parse(exhibitItem.header_style))
                "
              >
                <div class="d-inline-flex">
                  <div
                    class="header-btn"
                    v-for="(item, index) in header"
                    :key="index"
                    :style="
                      getExhibitButtonStyle(
                        JSON.parse(exhibitItem.header_style)
                      )
                    "
                    @click="exhibitHtml = item.content"
                  >
                    {{ item.title }}
                  </div>
                </div>
              </b-col>
            </b-row>
            <div v-html="exhibitHtml"></div>
          </div>
        </div>
      </div>
    </div>
    <b-modal
      ref="view_items_modal"
      id="view_items_modal"
      size="lg"
      centered
      hide-footer
    >
      <div v-for="(item, index) in viewItems" :key="index">
        <div class="d-flex flex-row">
          <img
            class="list-thumbnail responsive border-0"
            :src="getType(item.item)"
          />
          <div class="pl-2 d-flex flex-grow-1 min-width-zero">
            <div
              class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center"
            >
              <p class="list-item-heading mb-1 truncate">{{ item.title }}</p>
            </div>
          </div>
          <div class="my-auto mr-2">
            <button
              type="button"
              class="btn btn-outline-primary mb-1"
              @click="view(item)"
            >
              View
            </button>
            <b-button
              variant="primary"
              @click="addCartItem(item)"
              class="btn ml-2 btn-primary mb-1"
            >
              {{ getMark(item) ? '-' : '+' }} {{ $ct('cart') }}
            </b-button>
          </div>
        </div>
        <hr />
      </div>
    </b-modal>

    <b-modal ref="view_modal" id="view_modal" size="xl" centered hide-footer>
      <template v-slot:modal-header="{ close }">
        <h5>{{ viewItem.title }}</h5>
        <i class="close-btn simple-icon-close" @click="close()"></i>
      </template>
      <template v-if="viewItem.item">
        <template v-if="viewItem.item.substr(0, 4) == 'http'">
          <iframe class="w-100" :src="viewItem.item" style="height: 75vh" />
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
      </template>
    </b-modal>

    <b-modal
      ref="video_modal"
      id="video_modal"
      size="xl"
      centered
      hide-footer
      hide-header
    >
      <div :style="styleObject" class="video-modal">
        <i
          class="close-btn simple-icon-close"
          @click="$refs.video_modal.hide()"
        />
        <iframe
          :src="welcomeVideo"
          class="height-auto"
          width="1280"
          frameborder="0"
          allow="autoplay; fullscreen"
          allowfullscreen
        >
        </iframe>
      </div>
    </b-modal>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import axios from 'axios';
import mixins from '../../mixins/mixins';
import Sitebar from '../../containers/navs/Sitebar';
import ChatDesk from '../../containers/Chat/ChatDesk';

export default {
  middleware: 'site-auth',
  name: 'site',
  components: {
    sitebar: Sitebar,
    ChatDesk,
  },
  mixins: [mixins],
  props: ['cart-open', 'agenda-open'],
  metaInfo() {
    return { title: `${this.pageTitle}` };
  },
  data() {
    return {
      isShow: false,
      apiBase: '/site/',
      page: [],
      points: null,
      media: [],
      extension: '',
      viewItem: {},
      headItems: [],
      totalPages: [],
      modalItems: [],
      viewItems: [],
      modalTitle: '',
      viewTitle: '',
      fileExtension: '',
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
      exhibitHeaderStyle: {
        bg: '#922c88',
        height: 36,
        btn: {
          bg: '#d683ce',
          color: '#ffffff',
          radius: 4,
          space: 8,
        },
      },
      menus: [],
      exhibitHtml: '',
      boothItems: [],
      sponsorItems: [],
      posterItems: [],
      headerItems: [],
      videoItems: [],
      type: 'page',
      exhibitItems: [],
      exhibitItem: {},
      header: [],
      exhibitHtml: null,
      exhibitItemData: [],
      thisUrl: '',
      logo: '',
      currentIndex: '',
      height: 64,
      title: '',
      headerTop: '64px',
      pageTitle: 'Vizzi',
      welcomeVideo: null,
      format: null,
      helpChat: false,
      chatType: 0,
    };
  },
  methods: {
    refresh() {
      this.thisUrl = window.location.pathname;
      axios
        .post(this.apiBase + 'get', { domain: this.host, url: this.thisUrl })
        .then(res => {
          this.isShow = true;
          if (res.data.noData) {
            location.href = '/';
          } else {
            this.page = res.data.page;
            if (this.page.background) {
              this.extension = this.page.background.split('.').pop();
            }

            this.headItems = res.data.headItems;
            this.totalPages = res.data.totalPages;
            this.modalItems = res.data.modalItems;
            this.boothItems = res.data.boothItems;
            this.sponsorItems = res.data.sponsorItems;
            this.posterItems = res.data.posterItems;
            this.headerItems = res.data.headerItems;
            this.logo = res.data.logo;
            this.title = res.data.title;
            if (res.data.style) {
              this.headerStyle = JSON.parse(res.data.style);
              this.height = this.headerStyle.height;
            }
            if (
              !this.page ||
              (this.thisUrl != this.page.url &&
                this.thisUrl != '/' + this.page.url)
            ) {
              this.$router.push(this.page.url);
            }
            this.welcomeVideo = res.data.video;
            if (this.page) {
              if (this.page.type == 'content') {
                if (this.welcomeVideo) {
                  if (this.validURL(this.welcomeVideo)) {
                    this.format = 1;
                  } else {
                    this.format = 0;
                  }
                  this.$refs.video_modal.show();
                }
                this.points = JSON.parse(this.page.point);
                if (this.page.media) {
                  this.media = this.page.media.split(', ');
                }
              } else if (this.page.type == 'iframe') {
                this.type = 'iframe';
              } else {
                if (this.page.point) {
                  this.menus = JSON.parse(this.page.point);
                  this.exhibitHtml = this.menus[0].contentData;
                }
                if (this.page.style !== null) {
                  this.exhibitHeaderStyle = JSON.parse(this.page.style);
                }
              }
            }
          }
        });
    },
    getPage(item) {
      if (item.type.toLowerCase() == 'agenda') {
        this.$parent.$emit('agenda-open');
      } else if (item.type.toLowerCase() == 'cart') {
        this.$parent.$emit('cart-open');
      } else if (
        item.type == 'booth' ||
        item.type == 'poster' ||
        item.type == 'sponsor'
      ) {
        this.$router.push('/exhibit/' + item.url);
      } else if (item.type.toLowerCase() == 'iframe') {
        this.type = 'iframe';
        this.page = item;
        if (window.location.pathname != item.url) {
          this.$router.push(item.url);
        }
      } else if (item.type == 'agendaFilter') {
        this.$parent.$emit('agenda-filter-open', item.value);
      } else {
        this.type = 'page';
        if (item.type == 'modal') {
          this.viewItems = [];
          if (this.modalItems && this.modalItems.length > 0)
            this.modalItems.forEach(element => {
              if (element.page_id == item.id) {
                this.viewItems.push(element);
              }
            });
          if (this.viewItems.length == 1) {
            this.viewItem = this.viewItems[0];
            this.fileExtension = this.viewItem.item.split('.').pop();
            this.viewTitle = this.viewItem.title;
            this.$refs.view_modal.show();
          } else {
            this.modalTitle = item.title;
            this.$refs.view_items_modal.show();
          }
        } else {
          if (window.location.pathname != item.url) {
            this.$router.push(item.url);
            if (item.background) {
              this.page = item;
              this.extension = this.page.background.split('.').pop();
            } else {
              this.extension = '';
            }
            if (this.page && this.page.type == 'content') {
              this.points = JSON.parse(item.point);
            } else {
              this.menus = this.page ? JSON.parse(this.page.point) : [];
              if (this.page && this.page.style !== null) {
                this.exhibitHeaderStyle = JSON.parse(this.page.style);
              }
              if (this.menus.length) {
                this.exhibitHtml = this.menus[0].contentData;
              }
            }
          }
        }
      }
    },
    validURL(str) {
      var pattern = new RegExp(
        '^(https?:\\/\\/)?' + // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
          '(\\#[-a-z\\d_]*)?$',
        'i'
      ); // fragment locator
      return !!pattern.test(str);
    },
    getEvent(event) {
      this.helpChat = false;
      if (event.link) {
        if (event.link.substr(0, 4) == 'http') {
          window.location.replace(event.link);
        } else if (event.link == 'Chat') {
          this.chatType = event.type;
          this.helpChat = true;
        } else {
          if (event.link == 'Agenda By Day') {
            this.getPage({
              type: 'agendaFilter',
              value: event.value,
            });
          }
          this.totalPages.forEach(element => {
            if (element.title == event.link) {
              this.getPage(element);
            }
          });
        }
      }
    },
    chatClose() {
      this.helpChat = false;
    },
    getType(file) {
      const extension = file.split('.').pop();
      if (extension == 'mp4') {
        return '/assets/img/mp4.png';
      } else if (extension == 'pdf') {
        return '/assets/img/pdf.png';
      } else {
        return '/assets/img/pic.png';
      }
    },
    view(item) {
      this.viewItem = item;
      this.fileExtension = this.viewItem.item.split('.').pop();
      this.viewTitle = this.viewItem.title;
      this.$refs.view_modal.show();
    },
    getHtml(item) {
      this.exhibitHtml = item.contentData;
    },
    getHeaderStyle() {
      let position = 'absolute';
      if (this.headerTop == 0) {
        position = 'fixed';
      }
      return {
        'background-color': 'transparent',
        height: this.headerStyle.height + 'px',
        'z-index': 99,
        top: this.headerTop,
        position: position,
        left: 0,
      };
    },
    getButtonStyle() {
      let boxShadow =
        '0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12)';
      let bg = this.headerStyle.btn.bg;
      if (!this.headerStyle.btn.boxShadow) {
        boxShadow = 'none';
      }
      if (!this.headerStyle.btn.hasBg) {
        bg = 'transparent';
      }
      const buttonHeight =
        2 * Number(this.headerStyle.btn.paddingX) +
        Number(this.headerStyle.btn.fontSize);
      return {
        'background-color': 'transparent',
        color: 'transparent',
        'margin-left': this.headerStyle.btn.space + 'px',
        'margin-right': this.headerStyle.btn.space + 'px',
        'border-radius': this.headerStyle.btn.radius + 'px',
        'margin-top': (this.headerStyle.height - buttonHeight) / 2 - 5 + 'px',
        'font-size': this.headerStyle.btn.fontSize + 'px',
        'padding-top': this.headerStyle.btn.paddingX + 'px',
        'padding-bottom': this.headerStyle.btn.paddingX + 'px',
        'padding-left': this.headerStyle.btn.paddingY + 'px',
        'padding-right': this.headerStyle.btn.paddingY + 'px',
      };
    },
    pointStyle(data) {
      return {
        top: 'calc(' + Math.min(data.y, data.ey) + '%)',
        left: 'calc(' + Math.min(data.x, data.ex) + '%)',
        width: 'calc(' + Math.abs(data.ex - data.x) + '%)',
        height: 'calc(' + Math.abs(data.ey - data.y) + '%)',
        color: data.color,
        'font-size': data.size + 'px',
        cursor: 'pointer',
      };
    },
    getExhibitHeaderStyle(data) {
      if (data) {
        this.exhibitHeaderStyle = data;
      }
      return {
        'background-color': this.exhibitHeaderStyle.bg,
        height: this.exhibitHeaderStyle.height + 'px',
      };
    },
    getExhibitButtonStyle(data) {
      if (data) {
        this.exhibitHeaderStyle = data;
      }
      return {
        'background-color': this.exhibitHeaderStyle.btn.bg,
        color: this.exhibitHeaderStyle.btn.color,
        'margin-left': this.exhibitHeaderStyle.btn.space + 'px',
        'margin-right': this.exhibitHeaderStyle.btn.space + 'px',
        'border-radius': this.exhibitHeaderStyle.btn.radius + 'px',
        'margin-top': (this.exhibitHeaderStyle.height - 22) / 2 + 'px',
        cursor: 'pointer',
      };
    },
    getBooth() {
      this.thisUrl = window.location.pathname;
      this.type = 'exhibit';
      this.exhibitItems = this.boothItems;
      if (this.thisUrl !== '/booth') {
        this.$router.push('booth');
      }
    },
    nextBooth() {
      if (this.exhibitItems.length > this.currentIndex + 1) {
        this.currentIndex++;
        this.exhibitItem = this.exhibitItems[this.currentIndex];
        if (JSON.parse(this.exhibitItem.data)) {
          this.exhibitItemData = JSON.parse(this.exhibitItem.data);
        }
        this.header = [];
        for (let i = 0; i < this.headerItems.length; i++) {
          if (this.exhibitItem.id == this.headerItems[i].booth_id) {
            this.header.push(this.headerItems[i]);
          }
        }
        if (this.header.length) {
          this.exhibitHtml = this.headerItems[0].content;
        }
      }
    },
    backBooth() {
      if (this.currentIndex > 0) {
        this.currentIndex--;
        this.exhibitItem = this.exhibitItems[this.currentIndex];
        if (JSON.parse(this.exhibitItem.data)) {
          this.exhibitItemData = JSON.parse(this.exhibitItem.data);
        }
        this.header = [];
        for (let i = 0; i < this.headerItems.length; i++) {
          if (this.exhibitItem.id == this.headerItems[i].booth_id) {
            this.header.push(this.headerItems[i]);
          }
        }
        if (this.header.length) {
          this.exhibitHtml = this.headerItems[0].content;
        }
      }
    },
    viewExhibit(item, index) {
      this.type = 'exhibitItem';
      this.currentIndex = index;
      this.exhibitItem = item;
      if (JSON.parse(item.data)) {
        this.exhibitItemData = JSON.parse(item.data);
      }
      this.header = [];
      for (let i = 0; i < this.headerItems.length; i++) {
        if (item.id == this.headerItems[i].booth_id) {
          this.header.push(this.headerItems[i]);
        }
      }
      if (this.header.length) {
        this.exhibitHtml = this.headerItems[0].content;
      }
    },
    itemStyle(data) {
      return {
        top: 'calc(' + Math.min(data.y, data.ey) + '%)',
        left: 'calc(' + Math.min(data.x, data.ex) + '%)',
        width: 'calc(' + Math.abs(data.ex - data.x) + '%)',
        height: 'calc(' + Math.abs(data.ey - data.y) + '%)',
      };
    },
    getNameColor() {
      const code = this.headerStyle.bg.charCodeAt(1);
      let text = '';
      if (code < 55.5) {
        text = 'black';
      } else {
        text = 'white';
      }
      return {
        color: text,
      };
    },
    async logout() {
      await this.$store.dispatch('auth/logout');
      this.$router.push({ name: 'login' });
    },
    handleScroll(e) {
      if (window.pageYOffset > 64) {
        this.headerTop = 0;
      } else {
        this.headerTop = '64px';
      }
    },
    agendaView() {
      this.agendaType++;
    },
  },
  created() {
    window.addEventListener('scroll', this.handleScroll);
  },
  destroyed() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
      site: 'site/getSiteUser',
      startTime: 'site/getStartTime',
      endTime: 'site/getEndTime',
    }),
    host() {
      return window.location.host.split('.')[0];
    },
    siteStatus() {
      var now = new Date().getTime();
      var start = new Date(this.startTime).getTime();
      var end = new Date(this.endTime).getTime();
      return now >= start && now <= end;
    },
    styleObject() {
      return {
        '--color': this.headerStyle.bg,
      };
    },
  },
  watch: {
    page(val) {
      this.pageTitle = val.title;
    },
    sessionDate(val) {
      if (val != '') {
        this.sessionFilterItems = [];
        if (this.sessionItems && this.sessionItems.length > 0)
          this.sessionItems.forEach(item => {
            if (item.date == this.dateData[val]) {
              this.sessionFilterItems.push(item);
            }
          });
      } else {
        this.sessionFilterItems = this.sessionItems;
      }
      this.agendaType = 1;
    },
  },
  mounted() {
    this.refresh();
  },
};
</script>

<style>
#app {
  height: 100%;
}
.point {
  width: 12px;
  height: 12px;
}
.point:hover {
  cursor: pointer;
}
.modal-lg {
  width: 60vw;
}
@media (min-width: 992px) {
  .modal-lg {
    max-width: 100%;
  }
}
.exhibit-header .header-btn {
  padding: 0 4px;
  font-weight: 400;
  transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,
    box-shadow 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms,
    border 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
  font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;
  line-height: 1.75;
  letter-spacing: 0.02857em;
  text-transform: capitalize;
  box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, 0.2),
    0px 2px 2px 0px rgba(0, 0, 0, 0.14), 0px 1px 5px 0px rgba(0, 0, 0, 0.12);
}
.border-rounded {
  border-radius: 1rem;
}
.cursor-pointer {
  cursor: pointer;
}
.prevBooth {
  position: absolute;
  font-size: 15px;
  color: red;
  width: fit-content;
  top: 5%;
  background: white;
  padding: 0 12px;
  border-radius: 4px;
  font-weight: 900;
  cursor: pointer;
  left: 2%;
}
.nextBooth {
  position: absolute;
  font-size: 15px;
  color: red;
  width: fit-content;
  top: 5%;
  right: 2%;
  background: white;
  padding: 0 12px;
  border-radius: 4px;
  font-weight: 900;
  cursor: pointer;
}
.account {
  right: 5%;
  top: 0;
  cursor: pointer;
}
.modal-xl .modal-content {
  max-height: 80vh;
  background: lightgrey;
  border: solid 16px white;
  border-radius: 0;
}
.modal-xl .modal-content .modal-body {
  overflow: auto;
}
.modal-xl .modal-header {
  background: rgba(0, 0, 0, 0.4);
  color: white;
}
.video-modal {
  border: solid 12px var(--color);
  border-radius: 16px;
}
.video-modal .close-btn {
  background: var(--color);
  color: white;
  box-shadow: 0px 0px 1px 1px var(--color);
  top: 0;
  right: 0;
}
#video_modal___BV_modal_body_ {
  padding: 0;
}
#video_modal___BV_modal_content_ {
  border: none;
  border-radius: 20px;
}
.height-auto {
  height: 637px;
  width: 100%;
}
@media (min-width: 992px) {
  .modal-xl.modal-dialog {
    max-width: 800px !important;
  }
}
@media (min-width: 1200px) {
  .modal-xl.modal-dialog {
    max-width: 1140px !important;
  }
}

@media (min-width: 577px) {
  .modal-xl.modal-dialog {
    min-height: calc(100% - 3.5rem);
  }
}
@media (max-width: 1199px) {
  .height-auto {
    height: 440px;
  }
}
@media (max-width: 991px) {
  .height-auto {
    height: 270px;
  }
}
#view_modal___BV_modal_body_ {
  padding: 0 !important;
}
</style>
