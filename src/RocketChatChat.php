<?php

namespace RocketChat;

use Httpful\Request;
use RocketChat\Client;

class Chat extends Client {

    public $id;
    public $name;
    public $members = array();

    public function __construct($name, $members = array()){
        parent::__construct();
        if( is_string($name) ) {
            $this->name = $name;
        } else if( isset($name->_id) ) {
            $this->name = $name->name;
            $this->id = $name->_id;
        }
        foreach($members as $member){
            if( is_a($member, '\RocketChat\User') ) {
                $this->members[] = $member;
            } else if( is_string($member) ) {
                // TODO
                $this->members[] = new User($member);
            }
        }
    }

    /**
     * Post a message in this channel, as the logged-in user
     */
    public function postMessage( $text ) {
        $message = is_string($text) ? array( 'text' => $text ) : $text;
        if( !isset($message['attachments']) ){
            $message['attachments'] = array();
        }

        $response = Request::post( $this->api . 'chat.postMessage' )
            ->body( array_merge(array('channel' => '@'.$this->name), $message) )
            ->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            return $response->body;
        } else {
            if( isset($response->body->error) )	throw new \Exception( $response->body->error );
            else if( isset($response->body->message) ) throw new \Exception( $response->body->message );
            return false;
        }
    }

}

