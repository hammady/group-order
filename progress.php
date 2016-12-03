<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php'; ?>
<title>
Order Progress
</title>
</head>
<body>
<?php include 'frame.php';?>
<?php include 'checkid.php'; ?>
<?php
	include 'openCon.php';
	if(isset($_POST['delete']))
	{
		if($_SESSION['userClass'] == 'M' or $_SESSION['userClass'] == 'O')
		{
			$query = 'SELECT * FROM order_meal WHERE id ='.$_POST['orderMealId'];
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			if($row['count'] > 1)
			{
				$query = 'UPDATE order_meal SET count=count-1 WHERE id='.$_POST['orderMealId'];
				$result = mysql_query($query);
			}
			else
			{
				$query = 'delete from order_meal where id = '.$_POST['orderMealId'];
				$result = mysql_query($query);
			}
			//echo $query;
		}
	} 
?>
<h3>Order progress</h3>
This is the progress of order number <?php echo $_REQUEST['orderId'];?>
<br/>
<a href='addOrder.php?orderId=<?php echo $_REQUEST['orderId']?>'>Manage your order</a>
<table border=1 cellspacing=0>
	<tr><th><b>Person</b></th><th><b>Total per person</b></th><th><b>Quantity</b></th><th><b>Meal</b></th><th><b>Notes</b></th>
	<?php
		if($_SESSION['userClass']=='M' or $_SESSION['userClass'] == 'O')
		{
			echo "<th><b>Options</b></th>";
		} 
	?>
	</tr>
	<?php
		$query = 'SELECT user.id owner_id,user.full_name owner,count(*) meal_count,sum(price*count) total FROM order_meal,meal,user WHERE user.id = order_meal.owner_id and meal.id = order_meal.meal_id and order_id = '.$_REQUEST['orderId'].' GROUP BY user.id ORDER BY meal.id;';
		//echo $query;
		$result = mysql_query($query);
		$odd = true;
		$odd2 = true;
		while($row=mysql_fetch_array($result))
		{
//			echo '<tr class="'.($odd?"odd":"even").'">
//					<td rowspan = '.$row['meal_count'].' valign=top><b>'.$row['owner'].'</b></td>
//					<td rowspan = '.$row['meal_count'].' valign=top>'.$row['total'].'</td>';
//			$query2 = 'SELECT meal.name,order_meal.count,notes
//					FROM meal,order_meal
//					WHERE order_meal.meal_id = meal.id
//					and order_meal.owner_id = \''.$row['owner_id'].'\'
//					and order_meal.order_id='.$_REQUEST['orderId'].'';
//			//echo $query;
//			$result2 = mysql_query($query2);
//			$rowStarted=true;
//			while($row2 = mysql_fetch_array($result2))
//			{
//				if($rowStarted != true)
//				{
//					echo "<tr class=\"".($odd?"odd":"even")."\">";
//					$rowStarted=true;
//				}
//				echo "<td>".$row2['count']."</td><td>".$row2['name']."</td><td>".$row2['notes']."</td></tr>";
//				$rowStarted=false;
//			}
//			$odd = !$odd;
			echo '<tr class="'.($odd?"odd":"even").'">
					<td rowspan = '.($row['meal_count']+1).' valign=top><b>'.$row['owner'].'</b></td>
					<td rowspan = '.($row['meal_count']+1).' valign=top>'.$row['total'].'</td></tr>';
			$query2 = 'SELECT meal.name,order_meal.count,notes, order_meal.id orderMealId
					FROM meal,order_meal
					WHERE order_meal.meal_id = meal.id
					and order_meal.owner_id = \''.$row['owner_id'].'\'
					and order_meal.order_id='.$_REQUEST['orderId'].'';
			//echo $query;
			$result2 = mysql_query($query2);
			while($row2 = mysql_fetch_array($result2))
			{
				echo "<tr class=\"".($odd2?"odd":"even")."\">";
				echo "<td>".$row2['count']."</td><td>".$row2['name']."</td><td>".$row2['notes']."</td>";
				if($_SESSION['userClass'] == 'M' or $_SESSION['userClass']=='O')
				{
					echo "<td><form method='POST'><input type='hidden' name='orderMealId' value='".$row2['orderMealId']."'/><input type=submit name='delete' value='-1'/> </form></td>";
				}
				echo "</tr>";
			}
			$odd = !$odd;
			$odd2 = !$odd2;
			
		}
	?>
</table>
<?php include 'frameend.php';?>
</body>
</html>