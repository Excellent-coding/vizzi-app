<template>

    <div
        class="notitifation" style="postion:fixed; top:100px; width:90%; margin:0 auto;"
        ref="chatLogContainer"
    >
            <div role="alert" class="alert alert-success alert-dismissible" v-for="(message,index) in notifications"
                :key="index"
                >
                <div>{{message.entry}}</div>
            </div>
            

    </div>
</template>

<script>
import Switches from "vue-switches";
import Cookies from 'js-cookie'
import PubNubVue from 'pubnub-vue'


export default {
  data() {
    return {
      notifications: [],
      domain: Cookies.get('domain'),  
    };
  },
  methods: {
    getNotifications: function() {
        var readTimeToken = localStorage.getItem('lastToken')
        
        if (readTimeToken != null){
            readTimeToken = parseInt(readTimeToken)
        } else {
            readTimeToken = 0
        }
        
        this.$pnSubscribe({
            channels: [this.domain]
        })

        PubNubVue.getInstance().history({
            //end: readTimeToken + 2,
            channel: [this.domain],
            //count: 3, // how many items to fetch
            stringifiedTimeToken: true, // false is the default
        })
        .then(response => {
            console.log(response)
            this.notifications = response.messages

            response.messages.forEach(message => {
                if (readTimeToken < parseInt(message.timetoken)){
                    localStorage.setItem('lastToken', parseInt(message.timetoken))
                    readTimeToken = message.timetoken
                }
            })
            
        }).catch((err) => {
            console.log(error)
        })
    }
  },
  beforeDestroy() {
  },
  created() {
  },
  watch: {
  },
  mounted() {
    this.getNotifications()  },
};
</script>

<style lang="css">
</style>
