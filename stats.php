<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php'; ?>
<title>
Statistics
</title>
</head>
<body>
<?php include 'frame.php'; ?>
<h3>
Statistics
</h3>
<?php //prepare the data to be shown in one place!
	include 'openCon.php';
	//date of the first closed order
	$query = ("SELECT date_format(min(f.date_created),'%d-%m-%Y') date_created FROM foodorder f WHERE f.state <> 'C';");
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$firstOrderDate = $row['date_created'];
	//number of all closed orders except deleted ones
	$query = 'SELECT count(*) cnt FROM foodorder f WHERE f.state<>\'C\';';
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$orderCount = $row['cnt'];
	//count of users
	$query = 'SELECT count(u.id) cnt FROM user u';
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$userCount = $row['cnt'];
	//count of ordered meals
	$query = 'SELECT sum(count) cnt FROM order_meal o,foodorder f WHERE o.order_id = f.id and f.state <> \'C\';';
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$mealCount = $row['cnt'];
	//how much money spent
	$query = 'SELECT sum(o.count*m.price) total FROM order_meal o,meal m,foodorder f WHERE o.meal_id = m.id and o.order_id = f.id and f.state<> \'C\';';
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$totalMoneySpent = $row['total'];
	//count of shops
	$query = 'SELECT count(s.id) cnt FROM shop s';
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$shopCount = $row['cnt'];
?>
<p> Since <?php echo $firstOrderDate; ?>, <?php echo $orderCount; ?> orders have been done using this system. <?php echo $userCount; ?> users have ordered <?php echo $mealCount; ?> meals costing <?php echo $totalMoneySpent;?>LE from <?php echo $shopCount; ?> shops.</p>
<hr/>
<h4>Most popular shops:</h4>
<table border=1 cellspacing=0>
	<tr>
		<th>Shop name</th><th>Number of orders</th><th>Money spent (LE)</th>
	</tr>
	<?php
		$query = '	SELECT s.name,count(distinct f.id) orderCount,sum(o.count * m.price) moneySpent
					FROM shop s,foodorder f left join order_meal o on o.order_id = f.id left join meal m on o.meal_id=m.id
					WHERE s.id = f.voted_shop_id
					and f.state <> \'C\'
					GROUP BY s.id
					ORDER BY orderCount DESC;';
		$result = mysql_query($query);
		$odd = true;
		while($row = mysql_fetch_array($result))
		{
			echo '
			<tr class="'.($odd?"odd":"even").'">
				<td>'.$row['name'].'</td><td>'.$row['orderCount'].'</td><td dir=rtl>'.$row['moneySpent'].'</td>
			</tr>
			';
			$odd=!$odd;
		}
		
	?>
</table>
<h4>Most popular meals:</h4>
<table border=1 cellspacing=0>
	<tr>
		<th>Meal name</th><th>Shop name</th><th>Quantity</th><th>Price (LE)</th><th>Total Price (LE)</th>
	</tr>
	<?php
		$query = '	SELECT m.name,s.name shopName,sum(o.count) cnt,m.price,sum(o.count)*m.price total
					FROM meal m,order_meal o,shop s,foodorder f
					WHERE o.meal_id = m.id
					and m.shop_id = s.id
					and o.order_id = f.id
					and f.state <> \'C\'
					GROUP BY m.id
					ORDER BY cnt DESC;';
		$result = mysql_query($query);
		$odd=true;
		while($row = mysql_fetch_array($result))
		{
			echo '
			<tr class="'.($odd?"odd":"even").'">
				<td>'.$row['name'].'</td><td>'.$row['shopName'].'</td><td>'.$row['cnt'].'</td><td dir=rtl>'.$row['price'].'</td><td dir=rtl>'.$row['total'].'</td>
			</tr>
			';
			$odd = !$odd;
		}
		
	?>
</table>

<?php include 'frameend.php'; ?>
</body>
</html>