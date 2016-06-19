# inreach_custom_map
Connects with InReach KML feed to generate a custom map for your website.

# Setup
Enter your database information in the top of each of the scripts. You'll need to have a MySQL server set up at your web host with a database created for you to use. You should be able to use the same database as is used for Wordpress if you have that set up already.

Enter the InReach feed information at the top of update_map.php. Use the 'Raw KML Feed' provided by the MapShare webpage. By specifying a password you will be able to protect your raw feed from public access while still allowing this script to grab the data. Make sure to specify a date range with your feed link as the InReach website will only return the latest point if this isn't done.

Visit create_table.php on the web host. You should recieve a confirmation indicating the table has been created successfully.

Grab a Google Maps Javascript API key from the Google Developers website. I don't think you strictly need one, but your page may fail to load after a while if you don't.

You can now visit map.php. It will fail to load the first time, but you should be able to refresh and your map will be displayed with any points it was able to download.

# Restricting date range
If you only wish to display points on the map inside a certain range of dates, you can specify a date range by setting $d1 and $d2 in map.php. This will restrict the database query to only returning results between $d1 and $d2 days old. The default behaviour is to return points younger than 6 months old.
