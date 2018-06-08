<?php session_start();
require "vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
define( 'CONSUMER_KEY', getenv('CONSUMER_KEY'));
define( 'CONSUMER_SECRET', getenv('CONSUMER_SECRET'));
//localhost doesn't work  as a callback in the apps.twitter.com site, use 127.0.0.1 instead
define( 'OAUTH_CALLBACK', 'http://127.0.0.1/tweetmap_whitelabel/' );
if ( isset( $_REQUEST['oauth_verifier'] ) ) {
    $request_token = [];
    $request_token['oauth_token'] = $_SESSION['oauth_token'];
    $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
    if ( isset( $_REQUEST['oauth_token'] ) && $request_token['oauth_token'] !== $_REQUEST['oauth_token'] ) {
      // Abort! Something is wrong.
    }
    $connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret'] );
    $access_token = $connection->oauth( "oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']] );
    $_SESSION['access_token'] = $access_token;
    $access_token = $_SESSION['access_token'];
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $user = $connection->get("account/verify_credentials");
    $timeline = $connection->get("statuses/user_timeline", ["count" => 5, "exclude_replies" => true]);
    $markers = [];
    foreach($timeline as $item){      
      $geo = $item->geo;
       $markers[] = array(
        'Title'=>'Title','Desc'=>'',  'Lat'=> $geo['coordinates'][0], 'Long'=> $geo['coordinates'][1],'Image'=>'',
        );
    }
}else {
  $connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET );
  //ok so you only get this once. Its a temporary key.. (thats why we put it in a session)
  //this must be unique each time you do the initial request to get back the oauth_token and oauth_verifier
  $request_token = $connection->oauth( 'oauth/request_token', array( 'oauth_callback' => OAUTH_CALLBACK ) );
  $_SESSION['oauth_token'] = $request_token['oauth_token'];
  $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
  $url = $connection->url( 'oauth/authorize', array( 'oauth_token' => $request_token['oauth_token'] ) );
  echo '<br><br><a class="btn btn-primary" href="' . $url .  '">Next step: authorize on twitter.com</a>';
}
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
var map;
var infowindow = new google.maps.InfoWindow({
maxWidth: 200
});
function initialize() {
var mapOptions = {
  zoom: 10,
  <?php
?>
center: new google.maps.LatLng(
<?php
(isset($current_lat)) ? print $current_lat : print '37.68235356';?>,<?php (isset($current_long)) ? print $current_long : print '-1.68476679'; 
?>
),
 mapTypeId: google.maps.MapTypeId.HYBRID
};
<?php $js_array = json_encode($markers);
echo "var myMarkers = ". $js_array . ";\n";
die;
?>
//createmap
map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
//make a new marker with a title this could be an array and position could be lat longs from array
var oms = new OverlappingMarkerSpiderfier(map);
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
google.maps.event.addDomListener(window, 'load', initialize)
      $(function() {
        $.ajax({
          type: "GET",
          url: "/join.gpx",
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
