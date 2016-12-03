<html>
<head>
<title>
Exporting Shops Data
</title>
</head>
<body>
<?php
	include 'openCon.php';
	$query = 'SELECT * FROM shop;';
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		echo "<br/>INSERT INTO shop (id,name,phone_number,date_modified,delivery,ban_date,banned) VALUES (".$row['id'].",'".$row['name']."','".$row['phone_number']."','".$row['date_modified']."',".$row['delivery'].",'".$row['ban_date']."','N');";
	}
?>
<hr>
<?php
	include 'openCon.php';
	$query = 'SELECT * FROM meal;';
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		echo "<br/>INSERT INTO meal (id,name,description,price,shop_id,creator_id) VALUES (".$row['id'].",'".$row['name']."','".$row['description']."',".$row['price'].",".$row['shop_id'].",1);";
	}
?>
</body>
</html>