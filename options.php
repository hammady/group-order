<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php'; ?>
<title>
Options
</title>
</head>
<body>
<?php include 'frame.php'; ?>
<h3>
Options
</h3>
<?php
	include 'logincode.php';
?>
<?php
	include 'openCon.php';
	if(isset($_POST['submit']))
	{
		$query= 'UPDATE user SET ordersPerPage = '.$_POST['ordersPerPage'].' , receiveWhenNewOrder = \''. ($_POST['receiveWhenNewOrder']=='on'?'Y':'N').'\' WHERE id='.$_SESSION['userId'];
		$result = mysql_query($query);
		$_SESSION['ordersPerPage'] = $_POST['ordersPerPage'];
	} 
?>
<form action="options.php" method="post">
<input type="checkbox" name="receiveWhenNewOrder" <?php 
	$query = 'SELECT receiveWhenNewOrder,ordersPerPage FROM user WHERE id='.$_SESSION['userId'];
	$result = mysql_query($query);
	$row=mysql_fetch_array($result);
	if($row['receiveWhenNewOrder']=='Y')
		echo 'checked="checked"';
?> /> Send me an IP message when new orders are created. <br/>
Number of orders to show in a page:<input type=text name=ordersPerPage value=<?php 
	echo $row['ordersPerPage'];
?> /><br/>
<input type="submit" name="submit" value="Save options"/>
</form>
Meals you have ordered:
<table border=1 cellspacing=0>
	<tr><th>Name</th><th>Shop</th><th>Quantity</th><th>Cost</th></tr>
	<?php
		include 'openCon.php';
		$query = 'SELECT meal.name,shop.name shop_name,sum(order_meal.count) cnt ,sum(order_meal.count)*meal.price cost FROM order_meal,meal,shop WHERE order_meal.meal_id = meal.id and owner_id = '.$_SESSION['userId'].' and meal.shop_id = shop.id GROUP BY meal.id ORDER BY cnt DESC,name ASC';
		$result = mysql_query($query);
		$odd=true;
		while($row = mysql_fetch_array($result))
		{
			echo '<tr class="'.($odd?"odd":"even").'"><td>'.$row['name'].'</td><td>'.$row['shop_name'].'</td><td>'.$row['cnt'].'</td><td>'.$row['cost'].'</td></tr>';
			$odd=!$odd;
		}	 
	?>
	<tfoot>
		<tr>
			<td colspan=3>Total:</td><td><?php
		$query = 'select sum(cost) total from (SELECT meal.name,shop.name shop_name,sum(order_meal.count) cnt ,sum(order_meal.count)*meal.price cost FROM order_meal,meal,shop WHERE order_meal.meal_id = meal.id and owner_id = '.$_SESSION['userId'].' and meal.shop_id = shop.id GROUP BY meal.id ORDER BY cnt DESC,name ASC) meals';
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		echo $row['total'];
			?></td>
			</tr>
	</tfoot>
</table>
Your balance:<?php
$query = "SELECT balance FROM user WHERE id = ".$_SESSION['userId'];
$result = mysql_query($query);
$row = mysql_fetch_array($result);
$balance = $row['balance'];
echo $row['balance']; 
?>
<br/>
Transactions you have made:
<table border=1 cellspacing=0>
	<tr><th>ID</th><th>Date</th><th>Type</th><th>Order ID</th><th>Amount</th></tr>
	<?php
		$query = '
SELECT trans.id,date_format(date,\'%d-%m-%Y\') date ,amount,order_id,type
FROM trans
WHERE
user_id = '.$_SESSION['userId'].'
and trans.active = \'Y\'
ORDER BY trans.date';
		$result = mysql_query($query);
		$odd=true;
		while($row = mysql_fetch_array($result))
		{
			echo '<tr class="'.($odd?"odd":"even").'"><td>'.$row['id'].'</td><td>'.$row['date'].'</td><td>'.($row['type']=='C'?'Charge':'Order').'</td><td><a href="summary.php?orderId='.$row['order_id'].'">'.$row['order_id'].'</a></td><td>'.$row['amount'].'</td></tr>';
			$odd=!$odd;
		}	 
	?>
	<tfoot>
		<tr>
			<td colspan=4>Total:</td>
			<td><?php
			$query = "SELECT sum(amount) total FROM trans WHERE user_id=".$_SESSION['userId']." and type='C'";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			$totalCharged=$row['total'];
			$query = "SELECT sum(amount) total FROM trans WHERE user_id = ".$_SESSION['userId']." and type='O' and active = 'Y'";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			$totalOrdered = $row['total'];
			$total = $totalCharged - $totalOrdered;
			echo $total; 
			?></td>
		</tr>
	</tfoot>	
</table>
<?php
	if($balance == $total)
	{
		echo "Your balance coincides with your transactions.";
	} 
	else
	{
		echo '<p style="background-color: red; margin: 0;color:yellow">ERROR: YOUR BALANCE DOES NOT COINCIDE WITH YOUR TRANSACTIONS. REPORT THIS IMMEDIATLY TO ORDER SYSTEM ADMINISTRATORS.</p>'	;
	}
?>

<?php include 'frameend.php';?>

</body>
</html>