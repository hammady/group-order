<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php'; ?>
<title>
Make a new order
</title>
</head>
<body>
<?php include 'frame.php';?>
<h3>
Make a new order
</h3>
<?php
	include 'logincode.php';
?>

<?php
	if($_SESSION['userClass'] == 'M' || $_SESSION['userClass'] == 'O') 
	{
		if($_REQUEST['newOrder'] == 'entered')
		{
			include 'orderCreated.php';
		}
		else
		{
			include 'createOrder.php';
		}
	}
	else
	{
		echo "You cannot create a new order. Only moderators can create new orders. Please contract a moderator to create this order.";
	}
	
?>
<?php include 'frameend.php';?>
</body>
</html>