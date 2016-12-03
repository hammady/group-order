<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<head>
<title>
Order Summary
</title>
</head>
<body>
<?php include 'checkid.php'; ?>
<h1>
Summary page for order <?php echo $_REQUEST['orderId']; ?>
</h1>
<?php
	include 'openCon.php';
	$query = '
		SELECT 
			foodorder.subject,
			foodorder.state,
			user.full_name owner,
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
	//if($row['state'] == 'C')
	//	die("The order is not closed yet. You cannot view the summary now.");
?>
<table>
	<tr>
		<td><b>Number:</b></td><td><?php echo $_REQUEST['orderId'];?></td>
	</tr>
	<tr>
		<td><b>Subject:</b></td><td><?php echo $row['subject'];?></td>
	</tr>
	<tr>
		<td><b>Owner:</b></td><td><?php echo $row['owner']?></td>
	</tr>
	<tr>
		<td><b>Date created:</b></td><td><?php echo $row['date_created']?></td>
	</tr>
	<tr>
		<td><b>Time created:</b></td><td><?php echo $row['time_created']?></td>
	</tr>
	<tr>
		<td><b>Shop name:</b></td><td><?php echo $row['name']?></td>
	</tr>
	<tr>
		<td><b>Shop phone no:</b></td><td><?php echo $row['phone_number']?></td>
	</tr>
	<tr>
		<td><b>TSS phone no:</b></td><td>5501293</td>
	</tr>
	<tr>
		<td><b>TSS address:</b></td><td dir=rtl>برج نور الفجر 11 ش صديق شيبوب متفرع من جمال عبد الناصر فوق نفق سيدي بشر</td>
	</tr>
</table>
<hr>
<h3>Grouped by Persons</h3>
<?php
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
/*			$query = 'SELECT count(DISTINCT owner_id) cnt FROM order_meal WHERE order_id = '.$_REQUEST['orderId'];
			$result=mysql_query($query);
			$row = mysql_fetch_array($result);
			$usersCount = $row['cnt'];
			$userDelivery = $delivery / $usersCount;
			$userDelivery = ceil($userDelivery * 4) / 4;
			echo '<br/>Delivery is ' .$delivery.'LE.';
			echo '<br/>Every user will pay ' .$userDelivery.'LE.';
*/
			//get the delivery/total ratio
			$ratio = $delivery / $total;
			echo 'Prices include delivery.';
		}
		else
			echo "Prices do not include delivery.";
//<td><b>Delivery per person</b></td>
?>
<table border=1 cellspacing=0>
	<tr><td><b>Person</b></td><td><b>Total per person</b></td><td><b>المدفوع</b></td><td><b>الباقي</b></td><td><b>Quantity</b></td><td><b>Meal</b></td><td><b>Notes</b></td><td><b>Balance</b></td></tr>
	<?php
		$query = 'SELECT user.id owner_id,user.full_name owner,user.balance,count(*) meal_count,sum(price*count) total FROM order_meal,meal,user WHERE user.id = order_meal.owner_id and meal.id = order_meal.meal_id and order_id = '.$_REQUEST['orderId'].' GROUP BY user.id ORDER BY meal.id;';
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

			echo '<tr>
					<td rowspan = '.$row['meal_count'].' valign=top><b>'.$row['owner'].'</b></td>
					<td rowspan = '.$row['meal_count'].' valign=top>'.($userTotal).'</td>
					<td rowspan = '.$row['meal_count'].' valign=top>&nbsp</td>
					<td rowspan = '.$row['meal_count'].' valign=top>&nbsp</td>';
			$query2 = 'SELECT meal.name,order_meal.count,order_meal.notes
					FROM meal,order_meal
					WHERE order_meal.meal_id = meal.id
					and order_meal.owner_id = \''.$row['owner_id'].'\'
					and order_meal.order_id='.$_REQUEST['orderId'].'';
			//echo $query;
			$result2 = mysql_query($query2);
			$once = false;
			while($row2 = mysql_fetch_array($result2))
			{
				echo "<td>".$row2['count']."</td><td><font face=Tahoma>".$row2['name']."</font></td><td><font face=Tahoma>".$row2['notes']."</font></td>";
				if(!$once)
				{
					echo "<td rowspan = ".$row['meal_count']." valign=top><b>".$row['balance']."</b></td>";
					$once=true;
				}
				echo "</tr><tr>";
			}
			echo '</tr>';
			
		}
	?>
</table>
<p>Management and Change accumulation : <?php echo $paidDelivery - $delivery; ?>LE</p>
<hr>
<h3>Grouped by Meal</h3>
<table border=1 cellspacing=0>
	<tr><td><b>Meal</b></td><td><b>Notes</b></td><td><b>Quantity</b></td><td><b>Price</b></td><td><b>Total Price</b></td></tr>
	<?php
		$query = 'SELECT meal.id mealId,meal.name, null notes,sum(order_meal.count) quantity,sum(count*price) total,meal.price
				FROM order_meal,meal
				WHERE
				order_id='.$_REQUEST['orderId'].'
				and order_meal.meal_id = meal.id
				and meal_id is not null
				GROUP BY meal.name#,order_meal.notes
				ORDER BY meal.id;';
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result))
		{
			echo "<tr><td><font face=Tahoma>".$row['name']."</font></td><td><font face=Tahoma>".$row['notes']."</font></td><td>".$row['quantity']."</td><td>".$row['price']."</td><td>".$row['total']."</td></tr>";
			$query = '	SELECT distinct notes,sum(o.count) quantity,sum(count*price) total,m.price
						FROM order_meal o,meal m
						where
						o.meal_id = m.id
						and meal_id = '.$row['mealId'].'
						and order_id = '.$_REQUEST['orderId'].'
						and notes <> \'\'
						and notes is not null
						group by o.notes';
			$result2 = mysql_query($query);
			while($notesRow = mysql_fetch_array($result2))
			{
				echo "<tr><td><font face=Tahoma></font></td><td><font face=Tahoma>".$notesRow['notes']."</font></td><td>".$notesRow['quantity']."</td></tr>";
				//<td>".$notesRow['price']."</td><td>".$notesRow['total']."</td>
				//."";
			}
			
		}
		echo "<tr><td><b>Total</b></td><td><b>".($total + $delivery)."</b></td></tr>";
	?>
</table>
<hr>
<h4>
Notes:
</h4>
<ul>
	<li>Ask for total</li>
	<li>When will the food arrive?</li>
	<li>Write labels on meals (sandwiches, or dishes) </li>
	<li>Bring spoons for salads (if any)</li>
	<li>Bring ketchup (if needed)</li>
</ul>
<a href='index.php'>Home</a>
</body>
</html>
