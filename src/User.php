<?php
/**
 * Created by PhpStorm.
 * User: bahaaodeh
 * Date: 6/19/18
 * Time: 3:49 PM
 */

namespace Baha2Odeh\RocketChat;

use Httpful\Request;

class User
{

    public $api;
    public $id;

    public $error;
    public $errorType;

    public function __construct($api)
    {
        $this->api = $api;
    }

    /**
     * @param $user
     * @param $pass
     * @param $save_auth
     * @return bool|object
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function login($user, $pass, $save_auth = false)
    {
        $response = Request::post($this->api . 'login')
            ->body(array('user' => $user, 'password' => $pass))
            ->send();

        if ($response->code == 200 && isset($response->body->status) && $response->body->status == 'success') {
            if ($save_auth) {
                $tmp = Request::init()
                    ->addHeader('X-Auth-Token', $response->body->data->authToken)
                    ->addHeader('X-User-Id', $response->body->data->userId);
                Request::ini($tmp);
            }
            $this->id = $response->body->data->userId;
            return $response->body->data;
        } else {
            $this->error = $response->body->message;
            $this->errorType = $response->body->error;
        }
        return false;
    }

    /**
     * @param array $info
     * @throws \Httpful\Exception\ConnectionErrorException
     */
    public function register($info)
    {
        $response = Request::post($this->api . 'users.register')
            ->body($info)
            ->send();

        if ($response->code == 200 && isset($response->body->user)) {
            $this->id = $response->body->user->_id;
            return $response->body->user;
        } else {
            $this->error = $response->body->error;
            $this->errorType = !empty($response->body->errorType) ? $response->body->errorType : $response->body->error;
        }

        return false;
    }

    /**
     * Gets a user’s information, limited to the caller’s permissions.
     */
    public function info()
    {
        $response = Request::get($this->api . 'users.info?userId=' . $this->id)->send();

        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            $this->id = $response->body->user->_id;
            return $response->body;
        } else {
            $this->error = $response->body->error;
            $this->errorType = $response->body->error;
        }
        return false;
    }

    /**
     * Deletes an existing user.
     */
    public function delete()
    {

        // get user ID if needed
        if (!isset($this->id)) {
            $this->me();
        }
        $response = Request::post($this->api . 'users.delete')
            ->body(array('userId' => $this->id))
            ->send();

        if ($response->code == 200 && isset($response->body->success) && $response->body->success == true) {
            return true;
        } else {
            $this->error = $response->body->error;
            $this->errorType = $response->body->error;
        }
        return false;
    }
}
