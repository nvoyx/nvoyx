<?php


$post = $nvBoot->text($_POST);

if($post['lookup']==''){die();}

if(strlen($post['lookup'])<5){die();}

/* we have at least 5 characters, so lets try submitting to google maps */

/* sort out geographical location */
$loc = new \Geolocation\GeoLocation;
$coord = $loc->getGeocodeFromGoogle($post['lookup']);

/* do we have a valid location */
if($coord->status == 'OK'){
	
	/* grab the lat and lng values */
	$response['address']=$coord->results[0]->formatted_address;
	$response['lat']=$coord->results[0]->geometry->location->lat;
	$response['lng']=$coord->results[0]->geometry->location->lng;
	
	/* test that they are numeric */
	if(!is_numeric($response['lat']) || !is_numeric($response['lng'])){
		die();
	}
	
} else {
	die();
}

/* all good, so lets see it */
echo $nvBoot->json($response,'encode');