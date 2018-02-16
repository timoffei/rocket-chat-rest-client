<?php

namespace RocketChat;

use Httpful\Request;
use RocketChat\Client;

class Message extends Client {

    public $id;

    public function __construct($id){
        parent::__construct();
        
		$this->id = $id;
    }

    /**
     * Post a message in this channel, as the logged-in user
     */
    public function info() {
        $response = Request::get( $this->api . 'chat.getMessage?msgId=' . urlencode($this->id) )
            ->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            return $response->body;
        } else {
            if( isset($response->body->error) )	echo( $response->body->error . "\n" );
            else if( isset($response->body->message) )	echo( $response->body->message . "\n" );
            return false;
        }
    }

}

