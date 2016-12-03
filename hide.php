<?php
	session_start();
?>
<html>
<head>
<title>
Hide order
</title>
</head>
<body>
<?php include 'checkid.php'; ?>
<h1>
Hide order
</h1>
Hiding order <?php echo $_REQUEST['orderId'];?>
<?php
	include 'openCon.php';
	$query = 'SELECT owner_id FROM foodorder WHERE id = '.$_REQUEST['orderId'].';';
	//echo $query;
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if($_SESSION['userClass'] != 'M' and $_SESSION['userClass'] != 'O')
	{
		die('<br/>You are not authorized to hide this order. Only the administrator can hide it.');
	}
	$query = 'UPDATE foodorder SET state=\'H\' WHERE id =\''.$_REQUEST['orderId'].'\';';
	$result=mysql_query($query);
	echo '<br/>Your order has been hidden.';
	
?>
<br/>
<a href='index.php'>Home</a>
</body>
</html>
