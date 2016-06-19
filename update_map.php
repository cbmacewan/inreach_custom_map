<?php
// Information required to grab KML file.
$url = 'https://share.delorme.com/feed/Share/';
$username = '[this can be anything';
$password = '[your inreach feed password]';

// Connect to MYSQL database.
$mysql_servername = "127.0.0.1";
$mysql_username = "[mysql username]";
$mysql_password = "[mysql password]";
$mysql_dbname = "[mysql database]";

$conn = mysqli_connect($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);

// Check database connection was successful.
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Grab KML file from InReach website.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, 1);

$result = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

// Parse the KML file in to a format easy to work with.
$xml = new SimpleXMLElement($result);

$placemarks = $xml->Document->Folder->Placemark;
for ($i = 0; $i < sizeof($placemarks); $i++)
{
	$coord = $placemarks[$i];
	foreach(($coord->ExtendedData->Data) as $field)
	{
		$field_name = $field->attributes()->name;
		$value = (string) $field->value;
		
		$field_name = strtolower(str_replace(' ', '_', $field_name));
		
		// Intercept date fields and reformat to work with MySQL timestamp fields.
		if (($field_name == 'time_utc') || ($field_name == 'time'))
		{
			$value = date("Y-m-d H:i:s", strtotime($value));
		}
		
		// Field name id will conflict with id field in MySQL.
		if ($field_name == 'id')
		{
			$field_name = 'inreach_id';
		}
		
		$thispoint[$field_name] = mysqli_real_escape_string($conn, $value);
	}
	
	$thispoint['latitude_public'] = "0";
	$thispoint['longitude_public'] = "0";

	// Only add the data to the points array if it represents a valid record.
	// This is done by testing if an ID has been allocated to this record.
	if (array_key_exists('inreach_id', $thispoint)){
		$points[] = $thispoint;
	}
	
	// Clear it so we don't risk counting the same record twice.
	unset ($thispoint);
}

unset ($result);

// Update the database with any new records.
foreach ($points as $point)
{
	$query = "SELECT id FROM gpspoints WHERE inreach_id =" . $point['inreach_id'] . "";
	$result = mysqli_query($conn, $query);
	
	// If record doesn't exist add it to the database.
	if(mysqli_num_rows($result) == 0){
		// Generate the SQL query to add the points in to the database.
		$sql = "INSERT INTO gpspoints (inreach_id, time_utc, time, name, map_display_name, device_type, imei, incident_id, latitude, longitude, latitude_public, longitude_public, elevation, velocity, course, valid_gps_fix, in_emergency, text, event)
				VALUES ('" . $point['inreach_id'] . "','" . $point['time_utc'] . "','" . $point['time'] . "','" . 
				$point['name'] . "','" . $point['map_display_name'] . "','" . $point['device_type'] . "','" . 
				$point['imei'] . "','" . $point['incident_id'] . "','" . $point['latitude'] . "','" . 
				$point['longitude'] . "','" . $point['latitude_public'] . "','" . $point['longitude_public'] . "','" . 
				$point['elevation'] . "','" . $point['velocity'] . "','" . str_replace(' ° True', '',$point['course']) . "','" . 
				$point['valid_gps_fix'] . "','" . $point['in_emergency'] . "','" . $point['text'] . "','" . 
				$point['event']. "')";

		if (mysqli_query($conn, $sql)) {
			// We aren't displaying error messages.
		}
		
	}
}

?>