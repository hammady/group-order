<?php
// Heroku DB settings
if (isset($_ENV["CLEARDB_DATABASE_URL"])) 
{
  $db = parse_url($_ENV["CLEARDB_DATABASE_URL"]);
  define('DB_NAME', trim($db["path"],"/"));
  define('DB_USER', $db["user"]);
  define('DB_PASSWORD', $db["pass"]);
  define('DB_HOST', $db["host"]);
} 
else 
{
  define('DB_NAME', 'group-order');
  define('DB_USER', 'group-order');
  define('DB_PASSWORD', 'group-order');
  define('DB_HOST', '127.0.0.1');
}


$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if(!$con)
	die("Could not connect : " . mysql_error());
mysql_select_db(DB_NAME, $con) or die("Error selecting schema" . mysql_error());
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $con);
?>