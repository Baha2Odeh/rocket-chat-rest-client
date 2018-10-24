<?php

namespace Baha2Odeh\RocketChat;

use Httpful\Request;

class Group {

	public $id;
	public $name;
	public $members = array();
	public $api;

	public function __construct($api,$name, $members = array()){
		$this->api = $api;
		if( is_string($name)  || is_int($name)) {
			$this->name = $name;
		} else if( isset($name->_id) ) {
			$this->name = $name->name;
			$this->id = $name->_id;
		}else if(isset($name['id'])) {
			$this->name = !empty($name['name']) ? $name['name'] : ''; 
			$this->id = $name['id'];
		}else{
			$this->name = $name;
		}

        $this->members = is_array($members) ? $members : [];
	}

    /**
     * set loaded Group info
     * @param $name
     * @param $id
     * @return $this
     */
    public function getGroupInfo($name,$id){
        $this->name = $name;
        $this->id = $id;
        return $this;
    }
    /**
     * Creates a new private group.
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
	public function create(){


		$response = Request::post( $this->api . 'groups.create' )
			->body(array('name' => $this->name, 'members' => $this->members))
			->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			$this->id = $response->body->group->_id;
			return $response->body->group;
		}

		return false;
	}

    /**
     * Retrieves the information about the private group, only if you’re part of the group.
     * @return array|bool|object|string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
	public function info() {
		$response = Request::get( $this->api . 'groups.info?roomId=' . $this->id )->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			$this->id = $response->body->group->_id;
			return $response->body;
		}

		return false;
	}

    /**
     * Retrieves the information about the private group, only if you’re part of the group.
     * @return array|bool|object|string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
	public function history(){
		$response = Request::get( $this->api . 'groups.history?count=100000&roomId=' . $this->id )->send();

		if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
			return $response->body;
		}

		return false;
	}

    /**
     * Post a message in this group, as the logged-in user
     * @param $text
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
	public function postMessage( $text ) {
		$message = is_string($text) ? array( 'text' => $text ) : $text;
		if( !isset($message['attachments']) ){
			$message['attachments'] = array();
		}

		$response = Request::post( $this->api . 'chat.postMessage' )
			->body( array_merge(array('channel' => '#'.$this->name), $message) )
			->send();

		return ( $response->code == 200 && isset($response->body->success) && $response->body->success == true );
	}
    
	 /**
     * Removes the private group from the user’s list of groups, only if you’re part of the group.
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function archive(){
        $response = Request::post( $this->api . 'groups.archive' )
            ->body(array('roomId' => $this->id))
            ->send();

        return ( $response->code == 200 && isset($response->body->success) && $response->body->success == true );
    }
    /**
     * Removes the private group from the user’s list of groups, only if you’re part of the group.
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
	public function close(){
		$response = Request::post( $this->api . 'groups.close' )
			->body(array('roomId' => $this->id))
			->send();

		return ( $response->code == 200 && isset($response->body->success) && $response->body->success == true );
	}
	
 /**
     * delete the group
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
	public function delete(){
		$response = Request::post( $this->api . 'groups.delete' )
			->body(array('roomId' => $this->id))
			->send();

		return ( $response->code == 200 && isset($response->body->success) && $response->body->success == true );
	}	
	

    /**
     * Removes a user from the private group.
     * @param $user
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
	public function kick( $user ){
		// get group and user ids
		$userId = is_string($user) ? $user : $user->id;

		$response = Request::post( $this->api . 'groups.kick' )
			->body(array('roomId' => $this->id, 'userId' => $userId))
			->send();

		return ( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) ;
	}

    /**
     * Adds user to the private group.
     * @param $user
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
	public function invite( $user ) {

		$userId = is_string($user) ? $user : $user->id;

		$response = Request::post( $this->api . 'groups.invite' )
			->body(array('roomId' => $this->id, 'userId' => $userId))
			->send();

		return ( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) ;
	}


    /**
     * Gets a group's information, limited to the caller’s permissions.
     * @param $name
     * @return array|bool|object|string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getGroupInfoByName($name = null)
    {
        $name = empty($name) ? $this->name : $name;
        $response = Request::get($this->api . 'groups.info?roomName=' . $name)->send();

        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            return $response->body;
        }
        return false;
    }

}

