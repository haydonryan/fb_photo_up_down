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

//$FBPAGE_ID = '191589877477';  //Amnesia Photos page
$FBPAGE_ID = '629795797083447';  //retarded commedy page

print "Starting up\n";
print "Initialising SDK...";
#session_start();
print "Setting App ID: $APP_ID\n";
print "Setting App SECRET: $APP_SECRET\n";

FacebookSession::setDefaultApplication($APP_ID, $APP_SECRET);

print("Go To a browser and type in:\n");
print("https://www.facebook.com/dialog/oauth?client_id=$APP_ID&redirect_uri=https%3A%2F%2Fwww.amnesiaphotos.com%2Fconnect%2Flogin_success.html&scope=publish_stream,manage_pages,user_status&response_type=token\n");

// If you're making app-level requests:
//$session = FacebookSession::newAppSession();

// this has to come from the parameter sent to amnesia photos site
$session = new FacebookSession('CAAAACzWJ7UIBAOlkVg5ZCpzlGYgTHJ5wpZC3KukghQAgphH3rlNzmRjdn0wYznbJ15NPIIHQ1pxDp2WRdCh4DoDj9btdVPfwzYfSmwZCUbu8IUZAal5k0tYBdU8wTafTDCYjPMAiKpa35BhPkXZC1gcs9yTyNmZBfabryYFUsKMEJjKYf3wgt7uXS65tPaw9hVGNLkVEF34FoZChXa3muw3');


	$session =  LoginToFacebookPage ($session, $FBPAGE_ID );

//SetDate($session, $FBPAGE_ID, $page_access_token, $album,'2013-03-06T15:18:26-08:00');

	// Get All Subdirectory Names
	$dirs = array_filter(glob('albums/*'), 'is_dir');
	print_r( $dirs);
	foreach($dirs as $directory) {
		print "Uploading Directory $directory \n";
		// Create Gallery Name based on Directory
		$name = substr($directory, 7);
		if ($name[0] == '_') continue;
		print "Creating Album $name\n";
		$album = CreateAlbum ( $session, $name, '(C) Amnesia Photos. Please contact us if you would like to purchase full resolution copies' );
		if( preg_match('/[0-9]{8}/', $directory, $match)) 	{
			//print_r($match);
			$date = substr( $match[0],0,4) . "-" . substr($match[0],4,2) . "-" . substr($match[0],-2) . "-10:00";
			print "Using Date: $date\n";
			
			foreach(glob($directory.'/*.jpg') as $file) {
				print "Uploading ".$file . "\n";
				UploadPhoto($session, $album, $file,$date);
			}
		} else {
		//No Date found Using Today

			foreach(glob($directory.'/*.jpg') as $file) {
				print "Uploading ".$file . "\n";
				//UploadPhoto($session, $album, $file);
			}
	
		}
	}

// Get Date from Directory name

   //Get All file names
   //Upload File

// }

function LoginToFacebookPage ($session, $FBPAGE_ID )
{
//echo "Creating Album, $title\n";
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


return $session;
}

function UploadPhoto ( $session, $albumid, $filename, $time )
{
	if($session) {

		  try {
		   $response = (new FacebookRequest(      $session, 'POST', "/$albumid/photos", array(
			'source' => new CURLFile($filename, 'image/jpg'),
			'message' => '',
			'backdated_time' => $time, 
			'created_time' => $time,
			'updated_time' => $time
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


function SetDate ( $session, $FBPAGE_ID, $page_access_token, $id, $date )
{


	if($session) {
		  try {
		    // Upload to a user's profile. The photo will be in the
		    // first album in the profile. You can also upload to
		    // a specific album by using /ALBUM_ID as the path     
	echo "/$FBPAGE_ID/$id";
		  $response = (new FacebookRequest(      $session, 'POST', "/album/$id", array(
			'backdated_time' => '2013-03-06T15:18:26',
			'created_time' => '2013-03-06T15:18:26',
			'updated_time' => '2013-03-06T15:18:26',
			'access_token' => $page_access_token		      
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


function CreateAlbum ( $session, $albumName, $description )
{

	if($session) {
		  try {

		   $response = (new FacebookRequest(      $session, 'POST', "/$FBPAGE_ID/albums", array(
			'name' => $albumName,
			'message' => $description,
			'backdated_time' => '2013-03-06T15:18:26',
			'created_time' => '2013-03-06T15:18:26',
			'updated_time' => '2013-03-06T15:18:26'
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
