#!/usr/local/bin/php
<?php

/*
http://hayageek.com/facebook-dialog-oauth/
*/


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

$FBPAGE_ID = '191589877477';  //Amnesia Photos page


print "Starting up\n";


print "Initialising SDK...";
#session_start();
print "Setting App ID: $APP_ID\n";
print "Setting App SECRET: $APP_SECRET\n";

FacebookSession::setDefaultApplication($APP_ID, $APP_SECRET);

print("Go To a browser and type in:\n");
print("https://www.facebook.com/dialog/oauth?client_id=$APP_ID&redirect_uri=https%3A%2F%2Fwww.amnesiaphotos.com%2Fconnect%2Flogin_success.html&scope=publish_stream,manage_pages,user_status&response_type=token\n");

// If you're making app-level requests:
$session = FacebookSession::newAppSession();

// this has to come from the parameter sent to amnesia photos site
$session = new FacebookSession('CAAAACzWJ7UIBAOlkVg5ZCpzlGYgTHJ5wpZC3KukghQAgphH3rlNzmRjdn0wYznbJ15NPIIHQ1pxDp2WRdCh4DoDj9btdVPfwzYfSmwZCUbu8IUZAal5k0tYBdU8wTafTDCYjPMAiKpa35BhPkXZC1gcs9yTyNmZBfabryYFUsKMEJjKYf3wgt7uXS65tPaw9hVGNLkVEF34FoZChXa3muw3');

// To validate the session:
try {
  $session->validate();
} catch (FacebookRequestException $ex) {
  // Session not valid, Graph API returned an exception with the reason.
  echo $ex->getMessage()."\n";
} catch (\Exception $ex) {
  // Graph API returned info, but it may mismatch the current app or have expired.
  echo $ex->getMessage()."\n";
}

print "Geting Facebook Session" + "\n";

print "Getting Facebook Accounts for desired Page\n";

if($session) {
	try{
		$request = new FacebookRequest(	  $session, 'GET', '/me/accounts');
		$response = $request->execute();
		$graphObject = $response->getGraphObject()->asArray();
	  } catch(FacebookRequestException $e) {
	    echo "Exception occured, code: " . $e->getCode() . "\n";
    	    echo " with message: " . $e->getMessage() . "\n";
	  }   

	foreach($graphObject["data"] as $page) {
	echo $page->id;
	if($page->id == $FBPAGE_ID) {
		print "Found Token\n";
		$page_access_token = $page->access_token;
		break;
	}
	}
}

$session = new FacebookSession($page_access_token);

CreateAlbum ( $session, 'Test Album', 'test' );

/*
function LoginToFacebook ($title, $description )
{
echo "Creating Album, $title\n";

return 1;
}
*/
function UploadPhoto ( $session, $albumid,  $title, $description )
{
	echo "Creating Album, $title\n";

	if($session) {
		print "Have a Session\n";

		  try {

		    // Upload to a user's profile. The photo will be in the
		    // first album in the profile. You can also upload to
		    // a specific album by using /ALBUM_ID as the path     
		 	echo 'trying:';
			print '/'+$FBPAGE_ID+'/photos'; 

		   $response = (new FacebookRequest(      $session, 'POST', '/me/photos', array(
			'source' => new CURLFile('504708006080.jpg', 'image/jpg'),
			'message' => 'User provided message'
		      )
		    ))->execute()->getGraphObject();

		    echo "Posted with id: " . $response->getProperty('id') . "\n";

		  } catch(FacebookRequestException $e) {

		    echo "Exception occured, code: " . $e->getCode() . "\n";
		    echo " with message: " . $e->getMessage() . "\n";

		  }   

	}
	return 1;
}

function CreateAlbum ( $session, $albumName, $description )
{

	echo "Creating Album, $title\n";

	if($session) {
		  try {
		    // Upload to a user's profile. The photo will be in the
		    // first album in the profile. You can also upload to
		    // a specific album by using /ALBUM_ID as the path     
		 	echo 'trying:';
			print '/'+$FBPAGE_ID+'/p'; 

		   $response = (new FacebookRequest(      $session, 'POST', '/me/albums', array(
			'name' => $albumName,
			'message' => $description
			'backdated_time' => '2013-03-06T15:18:26-08:00';
		//	'privacy' => $albumName,
		      )
		    ))->execute()->getGraphObject();

		    echo "Posted with id: " . $response->getProperty('id') . "\n";
			return $response->getProperty('id');
		  } catch(FacebookRequestException $e) {

		    echo "Exception occured, code: " . $e->getCode() . "\n";
		    echo " with message: " . $e->getMessage() . "\n";

		  }   

	}
	return 0;
}


?>
