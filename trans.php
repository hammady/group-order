<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php'; ?>
<title>
Order Transactions
</title>
</head>
<body>
<?php include 'frame.php';?>
<h3>Order transactions</h3>
<?php include 'checkid.php';
	include 'logincode.php';
	include 'owneronly.php'; ?>
<?php
	include 'openCon.php';
	if(isset($_POST['pay']))
	{
		//Get the user ID of this transaction to update his balance
		//and checking if the transaction is already active
		//This is done to prevent double activating a transaction
		$query = 'SELECT user_id,active,amount FROM trans WHERE id = '. $_POST['transId'];
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$userId = $row['user_id'];
		$amount = $row['amount'];
		//Activate the transaction if it was not already active
		if($row['active'] <> 'Y')
		{
			//Activate transaction
			$query = "UPDATE trans SET active='Y' WHERE id=".$_POST['transId'];
			$result = mysql_query($query);
			
			//Update balance
			$query = "UPDATE user SET balance = balance - ".$amount." WHERE id = ".$userId;
			$result = mysql_query($query);
		}
	}
	if(isset($_POST['unpay']))
	{
		//Get the user ID of this transaction to update his balance
		//and checking if the transaction is already inactive
		//This is done to prevent double inactivating a transaction
		$query = 'SELECT user_id,active,amount FROM trans WHERE id = '. $_POST['transId'];
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$userId = $row['user_id'];
		$amount = $row['amount'];
		//Deactivate the transaction if it was active
		if($row['active'] == 'Y')
		{
			//Activate transaction
			$query = "UPDATE trans SET active='N' WHERE id=".$_POST['transId'];
			$result = mysql_query($query);
			
			//Update balance
			$query = "UPDATE user SET balance = balance + ".$amount." WHERE id = ".$userId;
			$result = mysql_query($query);
		}
		
	}
?>
<?php echo '<a href="summary.php?orderId='.$_REQUEST['orderId'].'">These are transactions of order number '.$_REQUEST['orderId'].'</a>';?>
<br/>
<table border=1 cellspacing=0>
	<tr><th>ID</th><th>User</th><th>Balance</th><th>Amount</th><th>Active</th><th>Type</th><th>Date</th><th>Options</th></tr>
	<?php
		$query = '
			SELECT trans.id,user.full_name,trans.amount,trans.active,trans.type,user.balance,date_format(trans.date,\'%d-%m-%Y\') date
			FROM trans,user
			WHERE
			trans.user_id = user.id
			and trans.order_id = '.$_REQUEST['orderId'] .'
			Order by trans.date,user.full_name';
		//echo $query;
		$result = mysql_query($query);
		$odd = true;
		$odd2 = true;
		while($row=mysql_fetch_array($result))
		{
			echo '<tr class="'.($odd?"odd":"even").'">
					<td valign=top>'.$row['id'].'</td>
					<td valign=top>'.$row['full_name'].'</td>
					<td valign=top>'.$row['balance'].'</td>
					<td valign=top>'.$row['amount'].'</td>
					<td valign=top>'.($row['active']=='Y'?'Active':'Inactive').'</td>
					<td valign=top>'.($row['type']=='O'?'Order':'Charge').'</td>
					<td valign=top>'.$row['date'].'</td>
					<td>';
			$amount =($row['amount']+0);
			$balance = ($row['balance']+0);  
			if( $amount <= $balance && $row['active']=='N')
			{
				echo '<form method=POST>
						<input type=submit value="Pay" name=pay />
						<input type=hidden value='.$row['id'].' name=transId />
					</form>';
			}
			if ($row['type'] == 'O' and $row['active'] == 'Y')
			{ //Give the option to unpay this transaction
				echo '<form method=POST>
						<input type=submit value="Unpay" name=unpay />
						<input type=hidden value='.$row['id'].' name=transId />
					</form>';
			}
			echo	'</td></tr>';
			$odd = !$odd;
			$odd2 = !$odd2;
			
		}
	?>
</table>
Order Total: <?php 
			$query = 'SELECT sum(count*price) total
					FROM order_meal,meal
					WHERE
					order_id='.$_REQUEST['orderId'].'
					and order_meal.meal_id = meal.id
					and meal_id is not null;';
			$result=mysql_query($query);
			$row = mysql_fetch_array($result);
			$total =$row['total'];
	$query = '
		SELECT 
			shop.delivery,
			foodorder.managed
		FROM 
			foodorder,shop
		WHERE 
			foodorder.voted_shop_id = shop.id 
			and foodorder.id ='.$_REQUEST['orderId'].';';
	//echo $query;
	$result = mysql_query($query);
	$row=mysql_fetch_array($result);
	$delivery=$row['delivery'];
	
	echo $total + $delivery;
			?>
<br/>
Total of paid orders: <?php
$query = 'SELECT ifnull(sum(ifnull(amount,0)),0) total FROM trans t WHERE order_id = '.$_GET['orderId'].' and active=\'Y\' and type=\'O\';';
$result = mysql_query($query);
$row = mysql_fetch_array($result);
$paid = $row['total'];
echo $row['total'];
?>
<br/>
Sa3eed should give me <?php echo $total + $delivery - $paid; ?>
<?php include 'frameend.php';?>
</body>
</html>