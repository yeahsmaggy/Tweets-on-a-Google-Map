<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
require "vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;


define( 'CONSUMER_KEY', 'NuYKWQHENes40tocqnmYNjhq4' );
define( 'CONSUMER_SECRET', '1TIlkzlSs3VVZ2G5zpijIfU7p9WNbQOc4c66EjRfTwz7AVg38F' );
define( 'OAUTH_CALLBACK', 'http://127.0.0.1/tweetmap_whitelabel/' );


$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET );

  //ok so you only get this once. Its a temporary key.. (thats why we put it in a session)
  //this must be unique each time you do the initial request to get back the oauth_token and oauth_verifier

  $request_token = $connection->oauth( 'oauth/request_token', array( 'oauth_callback' => OAUTH_CALLBACK ) );

  // print 'Request token: -----  <br><br>';
  // print_r( $request_token ) . '<br><br>';

  // echo 'oauth token:'. $request_token['oauth_token'] . '<br>';

  $_SESSION['oauth_token'] = $request_token['oauth_token'];
  $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

  // print '<br><br>Session: -----<br><br>';
  // print_r(  $_SESSION )  . '<br><br>';




  $url = $connection->url( 'oauth/authorize', array( 'oauth_token' => $request_token['oauth_token'] ) );
  echo '<br><br><a class="btn btn-primary" href="' . $url .  '">Next step: authorize on twitter.com</a>';



$request_token = [];
$request_token['oauth_token'] = $_SESSION['oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];



if (isset($_GET['oauth_token']) && $request_token['oauth_token'] !== $_GET['oauth_token']) {
    // Abort! Something is wrong.
}




$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);

print_r($connection);

die;

$access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_GET['oauth_verifier']]);

// print_r($access_token);






die;



// error_reporting( E_ALL );
// ini_set( 'display_errors', 1 );

// include 'eden.php';
// eden()->setLoader();

// //Instantiate Auth
// $auth = eden( 'twitter' )->auth( 'EWFFAW4hohsfgorQbHYKg', '2WBB5VpE8P4fQWzSDW87BkbRowbH27OCSy3dZgKM' );

// //Authentication
//  session_start();

// //if we have no access token
// if(!isset($_SESSION['access_token'], $_SESSION['access_secret'])) {
//   //if we do not have the request secret
//   if(!isset($_SESSION['request_secret'])) {
//       //we are still in the middle of authenticating
//       //get the request token from twitter
//       $token = $auth->getRequestToken();


//       //and store it in a session
//       $_SESSION['request_secret'] = $token['oauth_token_secret'];
//       //with the request token we can form a login URL the
//       //user will need to go to inorder to complete the authenication process
//       $login = $auth->getLoginUrl($token['oauth_token'], 'http://yourwebsite.com/auth');
//       header('Location:'.$login);
//       exit;
//   }

//   //either way we should have a request secret at this point
//   //at this point the user has already authorize our app
//   //if we got something back in a URL query
//   if(isset($_GET['oauth_token'], $_GET['oauth_verifier'])) {
//       //we need to convert that to an access token
//       $token = $auth->getAccessToken($_GET['oauth_token'], $_SESSION['request_secret'], $_GET['oauth_verifier']);
//       //lastly, save the token in session
//       $_SESSION['access_token']   = $token['oauth_token'];
//       $_SESSION['access_secret']  = $token['oauth_token_secret'];
//       //clean up
//       unset($_SESSION['request_secret']);
//   }
// }

// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';

// $timeline = eden( 'twitter' )->timeline( 'EWFFAW4hohsfgorQbHYKg', '2WBB5VpE8P4fQWzSDW87BkbRowbH27OCSy3dZgKM', $_SESSION['access_token'], $_SESSION['access_secret'] );

// $timeline->setCount( 50 );

// $timeline = $timeline->getUserTimelines( 'andrewwelch5' );

// $tweets = eden( 'twitter' )->tweets( 'EWFFAW4hohsfgorQbHYKg', '2WBB5VpE8P4fQWzSDW87BkbRowbH27OCSy3dZgKM', $_SESSION['access_token'], $_SESSION['access_secret'] );
?>


<!DOCTYPE html>
<html>
<head>
<title>Map</title>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<style>
    html, body, #map-canvas {
      margin: 0;
      padding: 0;
      height: 100%;
    }
  </style>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" ></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript" src="js/soundmanager2.js"></script>
<script type="text/javascript" src="js/oms.min.js"></script>
<script>
// soundManager.setup({
//   url: 'swf/',
//   debugMode: true
//   // preferFlash: false,
// });


var map;
var infowindow = new google.maps.InfoWindow({
maxWidth: 200
});


function initialize() {
//Start mapoptions
var mapOptions = {
  zoom: 10,
  <?php
if ( isset( $timeline[0]['geo']['coordinates'] ) ) {
  $current_lat = $timeline[0]['geo']['coordinates'][0];
  $current_long = $timeline[0]['geo']['coordinates'][1];
}
?>
  center: new google.maps.LatLng(<?php
/*(isset($current_lat)) ? print $current_lat :*/
print '37.68235356';
?>, <?php
/*(isset($current_long)) ? print $current_long :*/
print '-1.68476679';
?>),
  //'Lat'   : '37.68235356', 'Long'   : '-1.68476679',
  mapTypeId: google.maps.MapTypeId.HYBRID
};


//createmap
map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
//make a new marker with a title this could be an array and position could be lat longs from array
var oms = new OverlappingMarkerSpiderfier(map);

var myMarkers = new Array();

for (var i = 0; i < myMarkers.length; i++) {
  var marker = new google.maps.Marker({
    position: new google.maps.LatLng(myMarkers[i]['Lat'], myMarkers[i]['Long']),
    map: map,
    title: myMarkers[i]['Title']
  });
  marker.setTitle((i + 1).toString());
  attachInfo(marker, i, myMarkers[i]['Title'], myMarkers[i]['Desc'], myMarkers[i]['audioFileName'], myMarkers[i]['Image']);
};
function getInfoWindowEvent(marker, message) {
  infowindow.close()
  infowindow.setContent(message);
  infowindow.open(map, marker);
}
function attachInfo(marker, num, title, desc, audioFileName, image) {
  var message = '<h3 style="padding:0px; margin:0px;">' + title + '</h3>' + '<br/>' + desc + '<br/>' + '<a target="_blank" href="' + image + '"><img style="width:200px;" src="' + image + '"/></a>';
  google.maps.event.addListener(marker, 'click', function () {
  // oms.addListener('click', function(marker, event) {
    map.setZoom(14);
    map.setCenter(marker.getPosition());
    var message = '<h3 style="padding:0px; margin:0px;">' + title + '</h3>' + '<br/>' + desc + '<br/>' + '<a target="_blank" href="' + image + '"><img style="width:200px;" src="' + image + '"/></a>';
    getInfoWindowEvent(marker, message);
  });
  oms.addMarker(marker);  // <-- here
}
}
google.maps.event.addDomListener(window, 'load', initialize);
$(function() {
$.ajax({
type: "GET",
//url: "http://localhost:8888/tweetmap/join.gpx",
url: "http://www.andrewwelch.info/bajadamap/join.gpx",
dataType: "xml",
success: function(xml) {
var points = [];
var bounds = new google.maps.LatLngBounds ();
$(xml).find("trkpt").each(function() {
  var lat = $(this).attr("lat");
  var lon = $(this).attr("lon");
  var p = new google.maps.LatLng(lat, lon);
  points.push(p);
  bounds.extend(p);
});
var poly = new google.maps.Polyline({
  path: points,
  strokeColor: "#FF00AA",
  strokeOpacity: .7,
  strokeWeight: 4
});

poly.setMap(map);
map.fitBounds(bounds);
}
});
});
  </script>
</head>
<body>
<div id="map-canvas"></div>
</body>
</html>
