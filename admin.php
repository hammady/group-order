<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php';?>
<title>
Order System Administration
</title>
</head>
<body>
<?php include 'frame.php';?>
<h3>Administration</h3>
<?php
	//executing the commands sent by this page
	include 'openCon.php';
	if(isset($_POST['approve']))
	{
		$query = 'UPDATE user SET class=\'A\' WHERE id='.$_POST['id'].';';
		$result = mysql_query($query);
		$query = 'SELECT email FROM user WHERE id='.$_POST['id'].';';
		$result=mysql_query($query);
		$row=mysql_fetch_array($result);
		$stts = sendmail($row['email'], 'Membership approval', 'Your membership in Order System has been approved.');
		if(!$stts)
			echo 'Failed to send email!';
	}
	else if(isset($_POST['change']))
	{
		$query = 'UPDATE user SET email=\''.mysql_real_escape_string($_POST['email']).'\' WHERE id = '.mysql_real_escape_string($_POST['id']).';';
		$result = mysql_query($query);
	}
	else if(isset($_POST['charge']) and $_SESSION['userClass'] == 'O')
	{
		//Create a charge transaction
		$query = "INSERT INTO trans (user_id,amount,order_id,active,type,date) VALUES (".$_POST['userId'].",".$_POST['amount'].",null,'Y','C',now())";
		$result = mysql_query($query);
		//Increase user balance by charge amount
		$query = "UPDATE user SET balance=balance+".$_POST['amount']." WHERE id=".$_POST['userId'];
		$result = mysql_query($query);
	}
	if(isset($_POST['change']) and $_SESSION['userClass'] == 'O')
	{
		$query = "UPDATE cnfg SET default_shop_id = " .$_POST['shop'];
		$result = mysql_query($query);
	}
	if(isset($_REQUEST['impersonate']) and $_SESSION['userClass']=='O')
	{
		$query = 'SELECT * FROM user WHERE id = '.$_REQUEST['impersonate'];
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		if ($row) 
		{
				$_SESSION['loggedin'] = true;
				$_SESSION['userfullname'] = $row['full_name'];
				$_SESSION['username'] = $row['name'];
				$_SESSION['userId'] = $row['id'];
				//$_SESSION['userClass'] = $row['class'];
				$_SESSION['ordersPerPage'] = $row['ordersPerPage'];
				//$_SESSION['seenNote1'] = $row['seenNote1'];
				$_SESSION['balance'] = $row['balance'];
			
		}
	}
?>

<?php
	include 'logincode.php';
	include 'adminonly.php';
?>

<?php 
if($_SESSION['userClass'] == 'O')
{
	echo "Automatic orders will be created from:";
	echo "<br/>";
	echo "<form method=POST>";
	include 'openCon.php';
	//viewing all shops
	$result = mysql_query("SELECT id,name,datediff(now() , ban_date) days,ban_date FROM shop WHERE banned <> 'Y';");
	$result2 = mysql_query("SELECT default_shop_id FROM cnfg");
	$row2 = mysql_fetch_array($result2);
	$default_shop_id = $row2['default_shop_id'];
	while($row = mysql_fetch_array($result)) {
			echo "<input type='radio' name='shop' value='".$row['id']."'";
			if ($row['id'] == $default_shop_id)
			{
				echo "checked='checked'";
			}
			echo " />";
		echo $row['name'];
		echo "<br/>";
	}
	echo '<input type="submit" name="change" value="Change" />';
	echo "</form>";
}	
?>
Users:
<table border=1 cellspacing=0>
	<tr>
		<th>ID</th>
		<th>Username</th>
		<th>Full Name</th>
		<?php
			if ($_SESSION['userClass']== 'O')
				echo '		<th>Email</th>
				';
		?>
		<th>Class</th>
		<th>Options</th>
		<?php
			if($_SESSION['userClass'] == 'O')
				echo '<th>Balance</th>
				<th>Trans.</th>
				<th>Impersonate</th>';
		?>
	</tr>
	<?php
		include 'openCon.php';
		$query = 'SELECT * FROM `user` u;';
		$result = mysql_query($query);
		$odd = true;
		while($row=mysql_fetch_array($result))
		{
			$options = '&nbsp;';
			switch($row['class'])
			{
				case 'M':
					$class = 'Admin';
					break;
				case 'A':
					$class = 'Approved';
					break;
				case 'N':
					$class = 'Not Approved';
					$options = '<form method=post action=admin.php><input type=hidden name=id value='.$row['id'].' /><input type=submit name=approve value="Approve" /></form>';
					break;
				case 'O':
					$class = 'Owner';
					break;
				default:
					$class = 'Unknown';
			}
			echo '
				<tr class="'.($odd?"odd":"even").'">
					<td>'.$row['id'].'</td>
					<td>'.$row['name'].'</td>
					<td>'.$row['full_name'].'</td>';
			if ($_SESSION['userClass']== 'O')
			echo	'<td>
						<form method=POST>
							<input type=text name=email value="'.$row['email'].'" size=8 />
							<input type=hidden name=id value='.$row['id'].' />
							<input type=submit name=change value="Change Email" />
						</form>
					</td>';
			echo	'<td>'.$class.'</td>
					<td>'.$options.'</td>';
			if ($_SESSION['userClass']== 'O')
				echo '<td>
						'.$row['balance'].'
						<form method=POST>
							<input type=text name=amount size=1 />
							<input type=hidden name=userId value='.$row['id'].' />
							<input type=submit name=charge value=Charge size=3 />
						</form>
					</td>
					<td>
						<a href="userTrans.php?userId='.$row['id'].'">View</a>
					</td>
					<td>
						<a href="admin.php?impersonate='.$row['id'].'">'.$row['name'].'</a>
					</td>';
				echo '</tr>
			';
			$odd = !$odd;
		}
	?>
</table>
<?php include 'frameend.php';?>
</body>
</html>