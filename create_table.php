<?php

// Sign in to the MySQL database.
$mysql_servername = "127.0.0.1";
$mysql_username = "[mysql username]";
$mysql_password = "[mysql password]";
$mysql_dbname = "[mysql database]";
$conn = mysqli_connect($mysql_servername, $mysql_username, $mysql_password, $mysql_dbname);

$sql = "CREATE TABLE `gpspoints` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inreach_id` int(10) unsigned NOT NULL,
  `time_utc` datetime NOT NULL,
  `time` datetime NOT NULL,
  `name` tinytext COLLATE utf8_bin NOT NULL,
  `map_display_name` tinytext COLLATE utf8_bin NOT NULL,
  `device_type` tinytext COLLATE utf8_bin NOT NULL,
  `imei` tinytext COLLATE utf8_bin NOT NULL,
  `incident_id` int(11) NOT NULL,
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL,
  `latitude_public` float(10,6) NOT NULL,
  `longitude_public` float(10,6) NOT NULL,
  `elevation` tinytext COLLATE utf8_bin NOT NULL,
  `velocity` tinytext COLLATE utf8_bin NOT NULL,
  `course` tinytext COLLATE utf8_bin NOT NULL,
  `valid_gps_fix` tinytext COLLATE utf8_bin NOT NULL,
  `in_emergency` tinytext COLLATE utf8_bin NOT NULL,
  `text` tinytext COLLATE utf8_bin NOT NULL,
  `event` tinytext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

if ($conn->query($sql) === TRUE) {
    echo "Table gpspoints created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();


?>