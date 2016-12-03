<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php'?>
<title>
Menus
</title>
</head>
<body>
<?php include 'frame.php'; ?>
<h3>
Menus
</h3>
<?php
	include 'logincode.php';
//	include 'adminonly.php';
?>
<!-- This page MUST be called with the shop ID in post parameters-->
<?php
	include 'openCon.php';
	if(isset($_POST['command']))
	{
		if($_POST['command'] == 'add')
		{
			$query = 'INSERT INTO meal (name,description,price,shop_id,creator_id,type) VALUE (\''.mysql_real_escape_string($_POST['name']).'\',\''.mysql_real_escape_string($_POST['description']).'\','.mysql_real_escape_string($_POST['price']).','.$_REQUEST['shopId'].','.$_SESSION['userId'].',\''.$_REQUEST['type'].'\');';
			echo $query;
			$result=mysql_query($query);
		}
		if($_POST['command'] == 'update')
		{
			$query= 'UPDATE meal SET price='.$_POST['price'].' WHERE id = '.$_POST['meal_id'].';';
			$result=mysql_query($query);
		}
			if($_POST['command'] == 'delete')
		{
			$query= 'DELETE FROM meal WHERE id = '.$_POST['meal_id'].';';
			$result=mysql_query($query);
		}
			if($_POST['command'] == 'switch')
		{
			$query= 'UPDATE meal SET type = \''.$_POST['newtype'].'\' WHERE id = '.$_POST['meal_id'].';';
//			echo $query;
			$result=mysql_query($query);
		}
		if($_POST['command'] == 'deleteMenu')
		{
			$query = 'SELECT path FROM menu WHERE id='.$_REQUEST['menuId'];
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			unlink($row['path']);
			$query= 'DELETE FROM menu WHERE id = '.$_POST['menuId'].';';
			$result=mysql_query($query);
		}
		if($_POST['command'] == 'merge')
		{
			$query = 'select * from (
select count(*) cnt, om.order_id,om.owner_id from order_meal om,foodorder fo 
where fo.voted_shop_id = '.$_REQUEST['shopId'].' and om.order_id = fo.id
and meal_id in ('.$_POST['meal_id'].','.$_POST['to_meal_id'].')
group by om.order_id,om.owner_id) t
where cnt <> 1';
			$result = mysql_query($query);
			while($row = mysql_fetch_array($result))
			{
				$query2 = 'select sum(count) cnt from order_meal where order_id = '.$row['order_id'].' and owner_id = '.$row['owner_id'];
				$result2 = mysql_query($query2);
				$row2 = mysql_fetch_array($result2);
				$count = $row2['cnt'];
				$query2 = 'update order_meal set count = '.$count.'
where  order_id = '.$row['order_id'].' and owner_id = '.$row['owner_id'].' and meal_id = ' . $_POST['to_meal_id'];
				$result2 = mysql_query($query2);
				$query2 = 'DELETE from order_meal where order_id = '.$row['order_id'].' 
				and owner_id = '.$row['owner_id'].' and meal_id = ' . $_POST['meal_id'];
				$result2 = mysql_query($query2);
			}
			$query = 'UPDATE order_meal SET meal_id ='.$_POST['to_meal_id'].' WHERE meal_id = '.$_POST['meal_id'];
			$result = mysql_query($query);
			$query = 'DELETE from meal WHERE id = '. $_POST['meal_id'];
			$result = mysql_query($query);
			
		}
		$query = 'UPDATE shop SET date_modified=\''. date('Y-n-j').'\' WHERE shop.id = '.$_REQUEST['shopId'].';';
		//echo $query;
		$result = mysql_query($query);
	}
?>
Menu for <?php 
				include 'openCon.php';
				$query = 'SELECT name,date_format(date_modified,\'%e-%c-%Y\') date_modified FROM shop WHERE id = '.$_REQUEST['shopId'].';';
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				echo $row['name'];
				?>
<br/>
Date last modified:<?php echo $row['date_modified']; ?>
<table border=1 cellspacing=0>
<?php
	echo "<tr><th>Number</th><th>Name</th><th>Description</th><th>Price</th>";
	if($_SESSION['userClass'] == 'M' or $_SESSION['userClass'] == 'O')
		echo "<th>Options</th><th>Merge</th>";
	echo "<th>Type</th>";
	echo "</tr>";
	$query = 'SELECT * FROM meal WHERE shop_id='.$_REQUEST['shopId'].';';
	$result = mysql_query($query);
	$odd=true;
	While($row = mysql_fetch_array($result))
	{
		if($_SESSION['userClass']== 'M' or $_SESSION['userClass'] == 'O')
		{
			echo '<tr class="'.($odd?"odd":"even").'">
					<td>'.$row['id'].'</td>
					<td>'.htmlentities($row['name'],ENT_COMPAT,'UTF-8').'</td>
					<td>'.$row['description'].'</td>
					<td>
						<form method=post action="menus.php?shopId='.$_REQUEST['shopId'].'">
							<input type=text name="price" size=3 value='.$row['price'].' />
							<input type=hidden name=command value=update />
							<input type=hidden name=meal_id value='.$row['id'].' />
							<input type=submit value="Update" />
						</form>
					</td>
					<td>
						<form method=post action="menus.php?shopId='.$_REQUEST['shopId'].'">
							<input type=hidden name=command value=delete />
							<input type=hidden name=meal_id value='.$row['id'].' />
							<input type=submit value="Delete" />
						</form>
					</td>
					<td>
						<form method=post action="menus.php?shopId='.$_REQUEST['shopId'].'">
							<input type=text name=to_meal_id size=4 />
							<input type=hidden name=command value=merge />
							<input type=hidden name=meal_id value='.$row['id'].' />
							<input type=submit value="Merge" />
						</form>
					</td>
					<td>';

						echo '<form method=post action="menus.php?shopId='.$_REQUEST['shopId'].'">';
						if($row['type'] != 'D') {
							echo 'Static';
							$newType = 'D';
						} else {
							echo 'Dynamic';
							$newType = 'S';
						}
						echo '
							<input type=hidden name=command value=switch />
							<input type=hidden name=meal_id value='.$row['id'].' />
							<input type=hidden name=newtype value='.$newType.' />
							<input type=submit value="Switch" />
						</form>';
						echo '</td>
				</tr>';
		}
		else
		{
		echo '<tr class="'.($odd?"odd":"even").'">
				<td>'.$row['id'].'</td>
				<td>'.htmlentities($row['name'],ENT_COMPAT,'UTF-8').'</td>
				<td>'.$row['description'].'</td>
				<td>'.$row['price'].'</td>
			</tr>';
		}
		$odd = ! $odd;
	}
?>
</table>
<?php
	//if($_SESSION['userClass'] == 'M')
	{
		echo "<h3>Photo Menus:</h3>";
		$query = 'SELECT * FROM menu WHERE shop_id = '.$_REQUEST['shopId'];
		$result = mysql_query($query);
		echo '<table border=0>';
		while($row = mysql_fetch_array($result))
		{
			echo '<tr><td>'.$row['number'].'</td><td><a target="_blank" href="'.$row['path'].'"><img class="shopMenuThumb" src="'.$row['path'].'" /></a></td><td>'.$row['path'].'</td>';
			if($_SESSION['userClass']=='M' or $_SESSION['userClass'] == 'O')
			{
				//echo $_SESSION['userClass'];
				echo '<td>
						<form method="POST" action="menus.php?shopId='.$_REQUEST['shopId'].'">
							<input type=hidden name=command value="deleteMenu" />
							<input type=hidden name=menuId value="'.$row['id'].'"/>
							<input type=submit value="Delete" /></form></td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}
?>
<h3>Add a new photo:</h3>
<form method='post' action='newMenu.php?shopId=<?php echo $_REQUEST['shopId'];?>' enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
<input type='file' name='menu' size=40 />
<input type='submit' value='Add new menu image'/>
</form>
<h3>Add a new meal:</h3>
<p>Do NOT add false meals. Abusers will be banned!</p>
<form method='post' action="menus.php?shopId=<?php echo $_REQUEST['shopId'];?>">
	<div class="form">
		<ol>
		<li><label>Name:</label><input type=text name='name' /></li>
		<li><label>Description:</label><input type=text name='description' /></li>
		<li><label>Price:</label><input type=text name='price' /></li>
		<li><label>Type:</label><input type=radio name='type' value='S' checked=true />Static
		<input type=radio name='type' value='D' />Dynamic</li>
		<li class="submit"><input type=submit value='Add a new meal' /><input type=hidden name=command value=add /></li>
		</ol>
	</div>
</form>
<?php include 'frameend.php'; ?>
</body>
</html>