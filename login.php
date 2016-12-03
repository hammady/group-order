<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php';?>
<title>
Log in
</title>
<script type="text/javascript">
//<!--
function validate(form)
{
	with(form)
	{
		if(newpass.value != newpass2.value)
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
<?php include 'frame.php';?>
<h3>
Log in
</h3>
<?php
	include 'logincode.php';
	echo '<br/><a href="index.php">Home</a>';
?>
<?php
	if($_SESSION['loggedin'])
		echo '<meta http-equiv="REFRESH" content="1;url=index.php">';
?>
<?php include 'frameend.php';?>
</body>
</html>