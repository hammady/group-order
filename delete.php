<?php
	session_start();
?>
<html>
<head>
<title>
Delete order
</title>
</head>
<body>
<?php include 'checkid.php'; ?>
<h1>
Delete order
</h1>
Deleting order <?php echo $_REQUEST['orderId'];?>
<?php
	include 'openCon.php';
	if(!($_SESSION['userClass'] == 'M' || $_SESSION['userClass'] == 'O'))
	{
		die('<br/>You are not authorized to delete this order. Only the administrator or order owner can delete it.');
	}
//	$query = 'UPDATE foodorder SET state=\'H\' WHERE id =\''.$_REQUEST['orderId'].'\';';
		//Checking if there were paid transactions on this order
		$query = "SELECT * FROM trans WHERE order_id = ".$_REQUEST['orderId']." and type='O' and active='Y'";
		$result = mysql_query($query);
		//echo $query . '<br/>';
//		while($row = mysql_fetch_array($result)) 
//		{
//			//Update balance
//			$query2 = "UPDATE user SET balance = balance + ".$row['amount']." WHERE id = ".$row['user_id'];
//			$result2 = mysql_query($query2);
//			
//		}
		if($row = mysql_fetch_array($result))
		{ //There is active transactions on this order. Cannot reopen till all active transactions are unpaid
			echo "<br/>There is paid transactions on this order. You cannot delete order while it has paid transactions. Please unpay these transactions in order to delete this order.";
			echo '<meta http-equiv="REFRESH" content="5;url=trans.php?orderId='.$_REQUEST['orderId'].'">';			
		}
		else
		{
			$query = 'DELETE FROM trans WHERE order_id = '.$_REQUEST['orderId'].';';
			$result = mysql_query($query);
			$query = 'DELETE FROM order_meal WHERE order_id = '.$_REQUEST['orderId'];
			$result=mysql_query($query);
			$query = 'DELETE FROM foodorder WHERE id = '.$_REQUEST['orderId'];
			$result=mysql_query($query);
			
			echo '<br/>Your order has been deleted.';
		}
	
?>
<br/>
<a href='index.php'>Home</a>
</body>
</html>
