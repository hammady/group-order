<?php
	session_start();
?>
<html>
<head>
<title>
Close order
</title>
<?php
	include 'openCon.php';
//	if($_SESSION['userClass'] == 'M' || $_SESSION['userClass'] == 'O')
//		echo '<meta http-equiv="REFRESH" content="1;url=addOrder.php?orderId='.$_REQUEST['orderId'].'">';
?>
</head>
<body>
<?php include 'checkid.php'; ?>
<h1>
Reopen order
</h1>
Reopening order <?php echo $_REQUEST['orderId'];?>
<?php
	if($_SESSION['userClass'] != 'M' and $_SESSION['userClass'] != 'O')
	{
		echo '<br/>You are not authorized to reopen this order. Only the order owner can close it.';
	}
	else
	{
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
			echo "<br/>There is paid transactions on this order. You cannot reopen order while it has paid transactions. Please unpay these transactions in order to reopen this order.";
			echo '<meta http-equiv="REFRESH" content="5;url=trans.php?orderId='.$_REQUEST['orderId'].'">';			
		}
		else
		{
			$query = 'UPDATE foodorder SET state=\'C\' WHERE id =\''.$_REQUEST['orderId'].'\';';
			$result=mysql_query($query);
			$query = 'DELETE FROM trans WHERE order_id = '.$_REQUEST['orderId'].';';
			$result = mysql_query($query);
			echo '<br/>Your order has been reopened.';
			echo '<meta http-equiv="REFRESH" content="1;url=addOrder.php?orderId='.$_REQUEST['orderId'].'">';
		}
	}	
?>
<br/>
<a href='index.php'>Home</a>
</body>
</html>
