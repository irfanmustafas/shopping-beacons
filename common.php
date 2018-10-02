<?php

$rootUrl = 'https://' . getenv('HTTP_HOST') . '/'; // note: links *must* be https for beacons to appear in Google Nearby
$uploadsLocation = 'uploads/';
$numBeacons = 10;
$loginPin = 1234; // note: in this demo version there is a default PIN; for a real deployment, a per-site PIN should be used

$maxImageSize = 800; // pixels, maximum size of uploaded images
$maxTitleLength = 30; // characters, maximum length of site titles

$miniBrowser = false; // customise the interfaces depending on whether the user has a 'mini' or normal browser
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// various browsers have features that break javascript or parts of our upload interface - use a simpler interface for these
// UCMini / Opera Mini have proxies that break javascript, 
if (strpos($userAgent, 'UCWEB') === 0 || strpos($userAgent, 'Opera') === 0) {
	$miniBrowser = true;
}

// UCBrowser can't cope with uploads
if (strpos($userAgent, 'UCBrowser') !== false) {
	$miniBrowser = true;
}

// Stock Android browser struggles with the normal interface
if (strpos($userAgent, 'Android') !== false && strpos($userAgent, 'Chrome') === false && strpos($userAgent, 'Firefox') === false) {
	$miniBrowser = true;
}

?>
