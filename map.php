<?php
// Sign in to the MySQL database.
$mysql_servername = "127.0.0.1";
$mysql_username = "[mysql username]";
$mysql_password = "[mysql password]";
$mysql_dbname = "[mysql database]";
$conn = mysqli_connect($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);

// GPS coordinates query date range.
$d1 = "-6 months";
$d2 = "now";

// Import the GPS coordinates from the gpspoints table.
$query = "SELECT latitude, longitude FROM gpspoints WHERE (time_utc BETWEEN '" . date("Y-m-d H:i:s", strtotime($d1)) . "' AND '" . date("Y-m-d H:i:s", strtotime($d2)) ."') ORDER BY time_utc ASC";
$query = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($query)){
	$point['latitude'] = $row['latitude'];
	$point['longitude'] = $row['longitude'];
	$points[] = $point;
	unset ($point);
}		

$finalpt = end($points);

?>
<html>
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
		<meta charset="utf-8">
		<title>Tracking Map</title>
		<style>
			html, body {
			height: 100%;
			margin: 0;
			padding: 0;
			}
			#map {
			height: 100%;
			}
		</style>
	    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
	</head>
	<body>
		<img src="update_map.php" style="display: none;" />
		<div id="map"></div>
		<script>
			function initMap() {
				var map = new google.maps.Map(document.getElementById('map'), {
				zoom: 6,
				center: <?php echo "{lat: " . $finalpt['latitude'] . ", lng: " . $finalpt['longitude'] . "},\n"; ?>
				mapTypeId: google.maps.MapTypeId.TERRAIN
			});

			var routeCoordinates = [<?php
			$output = '';

			foreach ($points as $point) {
				$output .= "{lat: " . $point['latitude'] . ", lng: " . $point['longitude'] . "},\n";
			}

			// Remove trailing comma in points list.
			echo rtrim($output, ",\n"); ?>];
			
			// Plot the line.
			var routePath = new google.maps.Polyline({
			path: routeCoordinates,
			geodesic: true,
			strokeColor: '#FF0000',
			strokeOpacity: 1.0,
			strokeWeight: 2
			});

			// Show the current car/team position.
			var image = 'http://www.quarterlifecrisis.co.nz/tracking/car_icon.png';
			var carMarker = new google.maps.Marker({
			position: <?php echo "{lat: " . $finalpt['latitude'] . ", lng: " . $finalpt['longitude'] . "},\n"; ?>
			map: map,
			icon: image
			});

			routePath.setMap(map);
		
		}
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=[YOUR-API-KEY]&callback=initMap">
    </script>
  </body>
</html>
