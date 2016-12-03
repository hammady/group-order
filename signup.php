<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php'; ?>
<title>
Sign up
</title>
<script type="text/javascript">
//<!--
function validate(form)
{
	with(form)
	{
		if(pass.value != pass2.value)
		{
			alert("Password and confirm password must match!");
			return false;
		}
	}
	return true;
}
//-->
</script>
</head>
<body>
<?php include 'frame.php'?>
<h3>
Sign up
</h3>
Welcome new user,
<br/>
<?php
	include 'openCon.php';
	if(isset($_POST['submit']))
	{
		$query = 'SELECT * FROM user WHERE name=\''.mysql_real_escape_string($_POST['user']).'\'';
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		if(isset($row['id']))
		{
			echo "Username was chosen before. Please choose another username.<br/>";
			include 'signupform.php';
		}
		else
		{
			$group = mysql_real_escape_string($_POST['group']);
			if ($group == '')
			{
				$query = 'INSERT INTO user (name,password,full_name,email,group_name) VALUES (\''.mysql_real_escape_string($_POST['user']).'\',password(\''.mysql_real_escape_string($_POST['pass']).'\'),\''.mysql_real_escape_string($_POST['fullname']).'\',\''.$_POST['email'].'\',null);';
			}
			else
			{
				$query = 'INSERT INTO user (name,password,full_name,email,group_name) VALUES (\''.mysql_real_escape_string($_POST['user']).'\',password(\''.mysql_real_escape_string($_POST['pass']).'\'),\''.mysql_real_escape_string($_POST['fullname']).'\',\''.$_POST['email'].'\',\''.mysql_real_escape_string($_POST['group']).'\');';
			}
			$result = mysql_query($query) or die(mysql_error());
			echo "Membership request has been sent to the administrators. You will be notified when membership is approved. ";
			//get all moderators and notify them for the new comer!
			$query = 'SELECT email FROM user WHERE class=\'O\'';
			$result=mysql_query($query);
			while($row=mysql_fetch_array($result))
			{
				$stts = sendmail($row['email'], 'New member has joined.', 'Please click here http://ordersystem.x10.mx/admin.php to approve him.');
				if(!$stts)
					echo 'Failed to send email!';				
			}
		}
	}
	else
		include 'signupform.php';
		
?>
<?php include 'frameend.php';?>
</body>
</html>