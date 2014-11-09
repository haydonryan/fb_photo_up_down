#!/usr/local/bin/php
<?php

require 'vendor/autoload.php';
require_once 'secret.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;

print "Starting up\n";


print "Initialising SDK...\n";
#session_start();
FacebookSession::setDefaultApplication($APP_SECRET, $APP_SECRET);
#https://www.facebook.com/dialog/oauth?client_id=48142871874&redirect_uri=https%3A%2F%2Fwww.facebook.com%2Fconnect%2Flogin_success.html&response_type=token
#ab9db094319d13e702ae8a1df85a2eeb


#https://www.facebook.com/dialog/oauth?client_id=139730900025&redirect_uri=https%3A%2F%2Ffaceauth.appspot.com%2F%3Fversion%3D2100&scope=user_photos%2Cfriends_photos%2Cuser_likes%2Cuser_subscriptions&type=user_agent

#$helper = new FacebookRedirectLoginHelper('http://amnesiaphotos.com/facebook');
#$loginUrl = $helper->getLoginUrl();

$helper = new FacebookCanvasLoginHelper();
try {
  $session = $helper->getSession();
} catch (FacebookRequestException $ex) {
print "FB didn't work"; 
   // When Facebook returns an error
} catch (\Exception $ex) {
    // When validation fails or other local issues  
print "somethign else didn't work"; 
}


'87e2e9e00a3d649972e2613969252789';


print "Please go to " + $loginUrl + "\n";

print "Geting Facebook Session" + "\n";
//$session = new FacebookSession('access token here');

 $fp = fopen("php://stdin","r");
    fgets($fp);


if($session) {
print "Have a Session\n";

  try {

    // Upload to a user's profile. The photo will be in the
    // first album in the profile. You can also upload to
    // a specific album by using /ALBUM_ID as the path     
    $response = (new FacebookRequest(
      $session, 'POST', '/me/photos', array(
        'source' => new CURLFile('path/to/file.name', 'image/png'),
        'message' => 'User provided message'
      )
    ))->execute()->getGraphObject();

    // If you're not using PHP 5.5 or later, change the file reference to:
    // 'source' => '@/path/to/file.name'

    echo "Posted with id: " . $response->getProperty('id');

  } catch(FacebookRequestException $e) {

    echo "Exception occured, code: " . $e->getCode();
    echo " with message: " . $e->getMessage();

  }   

}
?>
