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
            if( isset($response->body->error) )	throw new Exception( $response->body->error );
            else if( isset($response->body->message) ) throw new Exception( $response->body->message );
            return false;
        }
    }

	public function update( $text, $roomId ) {
		$response = Request::post( $this->api . 'chat.update' )
            ->body( array('roomId' => $roomId, 'msgId' => $this->id, 'text' => $text) )
            ->send();
			
        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            return true;
        } else {
            if( isset($response->body->error) )	throw new Exception( $response->body->error );
            else if( isset($response->body->message) ) throw new Exception( $response->body->message );
            return false;
        }
	}
	
}

