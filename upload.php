#!/usr/local/bin/php
<?php

require 'vendor/autoload.php';
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;

print "Starting up\n";



FacebookSession::setDefaultApplication('48142871874', 'ab9db094319d13e702ae8a1df85a2eeb');


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