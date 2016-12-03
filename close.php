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
	if($_SESSION['userClass'] == 'M' || $_SESSION['userClass'] == 'O')
		echo '<meta http-equiv="REFRESH" content="1;url=summary.php?orderId='.$_REQUEST['orderId'].'">';
?>
</head>
<body>
<?php include 'checkid.php'; ?>
<h1>
Close order
</h1>
Closing order <?php echo $_REQUEST['orderId'];?>
<?php
	if($_SESSION['userClass'] != 'M' && $_SESSION['userClass']!= 'O')
	{
		echo '<br/>You are not authorized to close this order. Only the order owner can close it.';
	}
	{
		$query = 'UPDATE foodorder SET state=\'X\' WHERE id =\''.$_REQUEST['orderId'].'\';';
		$result=mysql_query($query);
		echo '<br/>Your order has been closed.';
		//Generating order transactions for this order.
		//All generated transactions must be generated as unactive
		
		//First, deleting all transactions based on this order before.
		//There may be transactions based on order if it was closed before then reopened.
		$query = 'DELETE FROM trans WHERE order_id = '.$_REQUEST['orderId'].';';
		$result = mysql_query($query);
		
		//Second, Calculating amount for every user in this order and create a transaction
	$query = '
		SELECT 
			foodorder.subject,
			foodorder.state,
			user.name owner,
			date_format(foodorder.date_created,\'%d-%m-%Y\') date_created,
			date_format(foodorder.date_created,\'%h:%i:%s%p\') time_created,
			shop.name,
			shop.phone_number,
			shop.delivery,
			foodorder.managed
		FROM 
			foodorder,shop,user
		WHERE 
			foodorder.voted_shop_id = shop.id 
			and user.id = foodorder.owner_id
			and foodorder.id ='.$_REQUEST['orderId'].';';
	//echo $query;
	$result = mysql_query($query);
	$row=mysql_fetch_array($result);
	if($row['managed'] == 'Y')
	{
		$query2 = 'SELECT management FROM cnfg';
		$result2 = mysql_query($query2);
		$row2 = mysql_fetch_array($result2);
		$management = $row2['management'];
	}
	else
	{
		$management = 0;
	} 
	$delivery=$row['delivery'];//+ $management;
	$query = 'SELECT sum(count*price) total
					FROM order_meal,meal
					WHERE
					order_id='.$_REQUEST['orderId'].'
					and order_meal.meal_id = meal.id
					and meal_id is not null;';
			$result=mysql_query($query);
			$row = mysql_fetch_array($result);
			$total =$row['total'];

		if($delivery != 0) 
		{
			//get the delivery/total ratio
			$ratio = $delivery / $total;
		}
		
	}	
		$query = '
		SELECT 
			user.id owner_id,
			user.full_name owner,
			count(*) meal_count,
			sum(price*count) total 
		FROM order_meal,meal,user 
		WHERE 
			user.id = order_meal.owner_id 
			and meal.id = order_meal.meal_id 
			and order_id = '.$_REQUEST['orderId'].' 
		GROUP BY user.id ORDER BY meal.id;';
		//echo $query;
		$result = mysql_query($query);
		$paidDelivery = 0;
		while($row=mysql_fetch_array($result))
		{
			$userDelivery = $row['total'] * $ratio;
			$userManagement = $row['total'] * $management / 100;
			$userTotal = ceil(($row['total'] + $userDelivery + $userManagement)*4)/4;
			$paidUserDelivery = $userTotal - $row['total'];
			$paidDelivery += $paidUserDelivery;
//					<td rowspan = '.$row['meal_count'].' valign=top>'.$paidUserDelivery.'</td>
			$query2 = '
			INSERT INTO trans (user_id,amount,order_id,active,type,date)
			VALUES ('.$row['owner_id'].','.$userTotal.','.$_REQUEST['orderId'].',\'N\',\'O\',now())';
			$result2 = mysql_query($query2);
			/* '
					<td rowspan = '.$row['meal_count'].' valign=top><b>'.$row['owner'].'</b></td>
					<td rowspan = '.$row['meal_count'].' valign=top>'.($userTotal).'</td>
					<td rowspan = '.$row['meal_count'].' valign=top>&nbsp</td>
					<td rowspan = '.$row['meal_count'].' valign=top>&nbsp</td>';
			*//*$query2 = 'SELECT meal.name,order_meal.count,order_meal.notes
					FROM meal,order_meal
					WHERE order_meal.meal_id = meal.id
					and order_meal.owner_id = \''.$row['owner_id'].'\'
					and order_meal.order_id='.$_REQUEST['orderId'].'';
			//echo $query;
			$result2 = mysql_query($query2);
			while($row2 = mysql_fetch_array($result2))
			{
				echo "<td>".$row2['count']."</td><td><font face=Tahoma>".$row2['name']."</font></td><td><font face=Tahoma>".$row2['notes']."</font></td></tr><tr>";
			}
			echo '</tr>';*/
			
		}
	
?>
<br/>
<a href='index.php'>Home</a>
</body>
</html>
