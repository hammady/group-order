<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php';?>
<title>Shops</title>
</head>
<body>
<?php include 'frame.php'; ?>
<h3>Shops</h3>
<?php
include 'logincode.php';
//	include 'adminonly.php';
?>
<?php
//First, do the action
include 'openCon.php';
if($_POST['command'] == 'add')
{
	$query = 'INSERT INTO shop (name,phone_number,delivery,ban_date,banned) VALUES (\''.mysql_real_escape_string($_POST['name']).'\','.mysql_real_escape_string($_POST['phone_number']).','.$_POST['delivery'].',\'0000-00-00 00:00:00\',\'N\');';
	//echo $query;
	$result= mysql_query($query) or die(mysql_error());
}
if($_POST['command']== 'ban')
{
	$query = 'UPDATE shop SET banned = \'Y\' WHERE id='.$_POST['shopId'].';';
	$result = mysql_query($query);
	//SELECT * FROM shop s WHERE datediff(now() , ban_date) > 7 or ban_date ='0000-00-00 00:00:00';

}
if($_POST['command']== 'unban')
{
	$query = 'UPDATE shop SET banned = \'N\' WHERE id='.$_POST['shopId'].';';
	$result = mysql_query($query);
	//SELECT * FROM shop s WHERE datediff(now() , ban_date) > 7 or ban_date ='0000-00-00 00:00:00';

}
//Second, view existing shops
//Third, Show the form to add a new shop
?>
<table border=1 cellspacing=0>
	<tr>
		<th>Number</th>
		<th>Name</th>
		<th>Phone Number</th>
		<th>Delivery</th>
		<th>Date of latest menu</th>
		<th>Menu</th>
		<?php if($_SESSION['userClass'] == 'M' or $_SESSION['userClass'] == 'O') echo "<th>Options</th>"; ?>
	</tr>
	<?php
	include 'openCon.php';
	$query = 'SELECT id,name,phone_number,date_format(date_modified,\'%e-%c-%Y\') date_modified,delivery FROM shop s;';
	$result = mysql_query($query);
	$odd = true;
	while($row=mysql_fetch_array($result))
	{
		echo "<tr class=\"".($odd?"odd":"even")."\"><td>".$row['id']."</td><td>".$row['name']."</td><td>".$row['phone_number']."</td><td>".$row['delivery']."</td><td>".$row['date_modified']."</td><td><a href='menus.php?shopId=".$row['id']."'>View</a></td>
		";
		if ($_SESSION['userClass'] == 'M'  or $_SESSION['userClass'] == 'O') {
			$query2 = 'SELECT banned FROM shop WHERE id ='.$row['id'];
			$result2 = mysql_query($query2);
			$row2 = mysql_fetch_array($result2);
			if($row2['banned'] == 'Y') {
			echo"<td>
						<form method=POST action=shops.php>
							<input type=hidden name=command value=unban />
							<input type=hidden name=shopId value=".$row['id']." />
							<input type=submit value=Unban />
						</form>
					</td>";
			} else {
			echo"<td>
						<form method=POST action=shops.php>
							<input type=hidden name=command value=ban />
							<input type=hidden name=shopId value=".$row['id']." />
							<input type=submit value=Ban />
						</form>
					</td>";
			}
		}
		echo "</tr>";
		$odd = ! $odd;
	}
	?>
</table>
<h3>Add a new shop:</h3>
<form method='post' action=shops.php>
<div class="form">
<ol>
	<li><label>Name:</label><input type=text name='name' /></li>
	<li><label>Phone No:</label><input type=text name='phone_number' /></li>
	<li><label>Delivery:</label><input type=text name='delivery' /></li>
	<li class="submit"><input type=submit value='Add a new shop' /><input
		type=hidden name=command value=add /></li>
</ol>
</div>
</form>
	<?php include 'frameend.php';?>
</body>
</html>
