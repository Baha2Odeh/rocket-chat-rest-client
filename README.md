# Rocket Chat REST API client in PHP

Use this client if you need to connect to Rocket Chat with a software written
in PHP, such as WordPress or Drupal.

## How to use

This Rocket Chat client is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "baha2odeh/yii2-rocket-chat-rest-client": "dev-master"
    }
}
```

And run composer to update your dependencies:

    $ php composer.phar update


After this, you have to register chat instance into components  
common/config/main-local.php
```php
 'components' => [
 	.....
 	'chat' => [
            'class' => '\Baha2Odeh\RocketChat\Rocket',
            'rocket_chat_instance' => 'http://rocket-chat-server:3000',
            'rest_api_root' => '/api/v1/'
        ],

 ]

```

Finally, instance the classes you need:
```php
$user = \Yii::$app->chat->user();

        $info = [
            'name'=>'name',
            'username'=>'username',
            'email'=>'username@email.com',
            'pass'=>'123123123'
        ];

        if(($userInfo = $user->login($info['username'],$info['pass'],true))){
            print_r($userInfo);
        }else if($user->register($info) &&  ($userInfo = $user->login($info['username'],$info['pass'],true))){
            print_r($userInfo);
        }else{
            die($user->error);
        }




        $group = \Yii::$app->chat->group('group-name',[$userInfo->userId]);

        $group->create();

        $group->postMessage('Hello world');
```

## Post a message
```php
// create a new channel
$channel = \Yii::$app->chat->channel( 'my_new_channel', array($newuser, $admin) );
$channel->create();
// post a message
$channel->postMessage('Hello world');
```
## Credits
This REST client uses the excellent [Httpful](http://phphttpclient.com/) PHP library by [Nate Good](https://github.com/nategood) ([github repo is here](https://github.com/nategood/httpful)).
