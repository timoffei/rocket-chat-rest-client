<?php

namespace RocketChat;

use Httpful\Request;
use RocketChat\Client;

class Channel extends Client {

	public $id = null;
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
	* Creates a new channel.
	*/
	public function create(){
		// get user ids for members
		$members_id = array();
		foreach($this->members as $member) {
			if( is_string($member) ) {
				$members_id[] = $member;
			} else if( isset($member->username) && is_string($member->username) ) {
				$members_id[] = $member->username;
			}
		}

		$response = Request::post( $this->api . 'channels.create' )
			->body(array('name' => $this->name, 'members' => $members_id))
			->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			$this->id = $response->body->channel->_id;
			return $response->body->channel;
		} else {
			echo( $response->body->error . "\n" );
			return false;
		}
	}

	/**
	* Retrieves the information about the channel.
	*/
	public function info() {
	    if($this->id !== null) {
            $response = Request::get( $this->api . 'channels.info?roomId=' . $this->id )->send();
        }
        else {
            $response = Request::get( $this->api . 'channels.info?roomName=' . $this->name )->send();
        }

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			$this->id = $response->body->channel->_id;
			return $response->body;
		} else {
			echo( $response->body->error . "\n" );
			return false;
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
			->body( array_merge(array('channel' => '#'.$this->name), $message) )
			->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			return $response->body;
		} else {
			if( isset($response->body->error) )	echo( $response->body->error . "\n" );
			else if( isset($response->body->message) )	echo( $response->body->message . "\n" );
			return false;
		}
	}
	
	/**
	* Delete a message in this channel, as the logged-in user
	*/
	public function deleteMessage($id) {
		$response = Request::post( $this->api . 'chat.delete' )
			->body( array('roomId' => $this->id, 'msgId' => $id) )
			->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			return $response->body;
		} else {
			if( isset($response->body->error) )	echo( $response->body->error . "\n" );
			return false;
		}
	}

	/**
	* Removes the channel from the user’s list of channels.
	*/
	public function close(){
		$response = Request::post( $this->api . 'channels.close' )
			->body(array('roomId' => $this->id))
			->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			return true;
		} else {
			echo( $response->body->error . "\n" );
			return false;
		}
	}

	/**
	* Delete the channel
	*/
	public function delete(){
		$response = Request::post( $this->api . 'channels.delete' )
			->body(array('roomId' => $this->id))
			->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			return true;
		} else {
			echo( $response->body->error . "\n" );
			return false;
		}
	}

	/**
	* Removes a user from the channel.
	*/
	public function kick( $user ){
		// get channel and user ids
		$userId = is_string($user) ? $user : $user->id;

		$response = Request::post( $this->api . 'channels.kick' )
			->body(array('roomId' => $this->id, 'userId' => $userId))
			->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			return true;
		} else {
			echo( $response->body->error . "\n" );
			return false;
		}
	}

	/**
	 * Adds user to channel.
	 */
	public function invite( $user ) {

		$userId = is_string($user) ? $user : $user->id;

		$response = Request::post( $this->api . 'channels.invite' )
			->body(array('roomId' => $this->id, 'userId' => $userId))
			->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			return true;
		} else {
			echo( $response->body->error . "\n" );
			return false;
		}
	}

	/**
	 * Adds owner to the channel.
	 */
	public function addOwner( $user ) {

		$userId = is_string($user) ? $user : $user->id;

		$response = Request::post( $this->api . 'channels.addOwner' )
			->body(array('roomId' => $this->id, 'userId' => $userId))
			->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			return true;
		} else {
			echo( $response->body->error . "\n" );
			return false;
		}
	}

	/**
	 * Removes owner of the channel.
	 */
	public function removeOwner( $user ) {

		$userId = is_string($user) ? $user : $user->id;

		$response = Request::post( $this->api . 'channels.removeOwner' )
			->body(array('roomId' => $this->id, 'userId' => $userId))
			->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			return true;
		} else {
			echo( $response->body->error . "\n" );
			return false;
		}
	}

}

