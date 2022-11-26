<template>
  <div class="iframe-container">
    <meta charset="utf-8">
    <link type="text/css" rel="stylesheet" href="https://dmogdx0jrul3u.cloudfront.net/1.3.7/css/bootstrap.css">
    <link
      type="text/css"
      rel="stylesheet"
      href="https://dmogdx0jrul3u.cloudfront.net/1.3.7/css/react-select.css"
    >

    <meta name="format-detection" content="telephone=no">
  </div>
</template>

<script>
import { ZoomMtg } from '@zoomus/websdk'

ZoomMtg.setZoomJSLib('https://source.zoom.us/1.8.1/lib', '/av');
// ZoomMtg.setZoomJSLib('https://jssdk.zoomus.cn/1.8.1/lib', '/av');

// ZoomMtg.setZoomJSLib('https://source.zoom.us/1.7.10/lib', '/av'); 

// ZoomMtg.setZoomJSLib('https://dmogdx0jrul3u.cloudfront.net/1.3.7/lib', '/av'); 

ZoomMtg.preLoadWasm()

ZoomMtg.prepareJssdk()

var API_KEY = 'uMLzUMzc7Z1pu21E4bLcDCHFDsgVT4AD6lnM';

var API_SECRET = '4iWOXN7bPP1LCiCCEJiKupz44ASzaYWMevNK';

export default {
  name: "ZoomFrame",
  data() {
    return {
      src: "",
      meetConfig: {},
      signature: {}
    };
  },
  props: {
    meetingId: String
  },
  created() {
    // Meeting config object
    this.meetConfig = {
      apiKey: API_KEY,
      apiSecret: API_SECRET,
      meetingNumber: 8817705540,
      userName: 'luis',
      passWord: "",
      leaveUrl: "https://zoom.us",
      role: 0
    };

    // Generate Signature function
    this.signature = ZoomMtg.generateSignature({
      meetingNumber: this.meetConfig.meetingNumber,
      apiKey: this.meetConfig.apiKey,
      apiSecret: this.meetConfig.apiSecret,
      role: this.meetConfig.role,
      success: function(res) {
        console.log(res)
      }
    });

    console.log(this.signature)
    ZoomMtg.init({
      leaveUrl: "http://www.zoom.us",
      screenShare: true,
      isSupportChat: true,
      isSupportAV: true,
      success: () => {
        ZoomMtg.join({
          meetingNumber: 4695337978,
          userName: this.meetConfig.userName,
          signature: this.signature,
          apiKey: this.meetConfig.apiKey,
          userEmail: "puchenglie@gmail.com",
          passWord: this.meetConfig.passWord,
          success: function(res) {
            // eslint-disable-next-line
          },
          error: function(res) {
            // eslint-disable-next-line
          }
        });
      },
      error: function(res) {
        // eslint-disable-next-line
      }
    });
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .iframe-container {
    width: 100px;
    height: 100px;
  }
</style>
