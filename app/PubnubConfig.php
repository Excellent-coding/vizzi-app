<?php

namespace App;

use PubNub\PubNub;
use PubNub\Enums\PNStatusCategory;
use PubNub\Callbacks\SubscribeCallback;
use PubNub\PNConfiguration;

class PubnubConfig extends SubscribeCallback {
   
    // Variables
    protected $pubnub = '';
    protected $pnconf = '';

    // Initialization
    public function __construct($uuid) {
        $this->pnconf = new PNConfiguration();
        $this->pubnub = new PubNub($this->pnconf);
        $this->pnconf->setPublishKey("pub-c-99b53823-bd09-4a9a-a2c7-7e75e5a6dea4");
        $this->pnconf->setSubscribeKey("sub-c-49812e5a-f539-11ea-8db0-569464a6854f");
        $this->pnconf->setSecretKey("sec-c-YTU1NDA0OTctNzc2YS00NDYxLTlhMDgtNmRjNTgwMmJkZjNh");
        $this->pnconf->setAuthKey($uuid);
    }

    // Override
    function status($pubnub, $status) {}
    function message($pubnub, $message) {}
    function presence($pubnub, $presence) {}

    // Publish a message to a channel
    public function publishMessage($message,$channel) {
        return $this->pubnub->publish()->channel($channel)->message($message)->sync();
    }
    
    // Grant Access (PAM)
    // Grant Access to Global Chat
    public function grantGlobal($uuid) {
       return $this->pubnub->grant()->read(true)->write(true)->channels("global","control")->authKeys($uuid);
    }   

    // Grant access to one on one chats
    public function grantOne($uuid1, $uuid2) {
        $channel = $uuid1."-".$uuid2;
        return $this->pubnub->grant()->read(true)->write(true)->channels($channel)->authKeys($uuid1, $uuid2);
    }

    // Delete messages from a channel
    public function deleteAllMessages($channel) {
        return $this->pubnub->deleteMessages()->channel($channel)->end(0)->sync();
    }

}