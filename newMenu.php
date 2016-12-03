<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<meta http-equiv="REFRESH" content="1;url=menus.php?shopId=<?php echo $_REQUEST['shopId'];?>">
<title>
Upload menu
</title>
</head>
<body>
<?php
	include 'openCon.php';
	$path = "m/".$_REQUEST['shopId']."/".$_FILES['menu']['name'];
	$query = "SELECT * FROM menu WHERE path='".$path."'";
	$result=mysql_query($query);
	if($row=mysql_fetch_array($result))
	{
		echo "Error: photo with same name already exists, change filename then upload again.";
	}
	else
	{
		$query = 'SELECT ifnull(max(number),0) + 1 newNo FROM menu WHERE shop_id = '.$_REQUEST['shopId'];
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$newNumber = $row['newNo'];
 		$query = "INSERT INTO menu (number, path, shop_id) values (".$newNumber.",'".$path."',".$_REQUEST['shopId'].")";
		$result = mysql_query($query);
		if(! is_dir(dirname($path)))
		{
			mkdir(dirname($path),0777,true);
		}
		if (move_uploaded_file($_FILES['menu']['tmp_name'],"m/".$_REQUEST['shopId']."/".$_FILES['menu']['name']) == FALSE) 
		{
			echo "Error";
		}
		else
		{
			echo "Added successfully";
		}
	}
?>
</body>
</html>
