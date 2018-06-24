<?php
/**
 * Created by PhpStorm.
 * User: bahaaodeh
 * Date: 6/19/18
 * Time: 3:53 PM
 */

namespace Baha2Odeh\RocketChat;

use Httpful\Request;

class Channel
{
    public $id;
    public $name;
    public $members = array();
    protected $api;
    public function __construct($api,$name, $members = array()){
        $this->api = $api;
        if( is_string($name) ) {
            $this->name = $name;
        } else if( isset($name->_id) ) {
            $this->name = $name->name;
            $this->id = $name->_id;
        }else if(isset($name['id'])) {
            $this->name = !empty($name['name']) ? $name['name'] : ''; 
            $this->id = $name['id'];
        }
        $this->members = is_array($members) ? $members : [];
    }

    /**
     * set loaded channel info
     * @param $name
     * @param $id
     * @return $this
     */
    public function setChannelInfo($name,$id){
        $this->name = $name;
        $this->id = $id;
        return $this;
    }

    /**
     * create new channel
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws \Exception
     */
    public function create(){
        $response = Request::post( $this->api . 'channels.create' )
            ->body(array('name' => $this->name, 'members' => $this->members))
            ->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            $this->id = $response->body->channel->_id;
            return $response->body->channel;
        }

        return false;
    }

    /**
     * Retrieves the information about the channel.
     * @return array|bool|object|string
     * @throws \Httpful\Exception\ConnectionErrorException
     * @throws \Exception
     */
    public function info() {
        $response = Request::get( $this->api . 'channels.info?roomId=' . $this->id )->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
            $this->id = $response->body->channel->_id;
            return $response->body;
        }
        return false;
    }

    /**
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
     * @return array|bool|object|string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function history(){
        $response = Request::get( $this->api . 'channels.history?roomId=' . $this->id )->send();

        if( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) {
    
            return $response->body;
        }

        return false;
    }

    /**
     * Removes the channel from the user’s list of channels.
     * @throws \Httpful\Exception\ConnectionErrorException
     * @return bool
     */
    public function close(){
        $response = Request::post( $this->api . 'channels.close' )
            ->body(array('roomId' => $this->id))
            ->send();

        return ( $response->code == 200 && isset($response->body->success) && $response->body->success == true );
    }

    /**
     * Removes a user from the channel.
     * @param $user
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function kick( $user ){
        // get channel and user ids
        $userId = is_string($user) ? $user : $user->id;

        $response = Request::post( $this->api . 'channels.kick' )
            ->body(array('roomId' => $this->id, 'userId' => $userId))
            ->send();

        return ( $response->code == 200 && isset($response->body->success) && $response->body->success == true );
    }

    /**
     * @param $user
     * @return bool
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function invite( $user ) {

        $userId = is_string($user) ? $user : $user->id;

        $response = Request::post( $this->api . 'channels.invite' )
            ->body(array('roomId' => $this->id, 'userId' => $userId))
            ->send();

        return ( $response->code == 200 && isset($response->body->success) && $response->body->success == true ) ;
    }



    /**
     * Gets a channel's information, limited to the caller’s permissions.
     * @param $name
     * @return array|bool|object|string
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function getChannelInfoByName($name = null)
    {
        $name = empty($name) ? $this->name : $name;
        $response = Request::get($this->api . 'channels.info?roomName=' . $name)->send();

        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            return $response->body;
        }
        return false;
    }
}