<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php'?>
<title>
User Transactions
</title>
</head>
<body>
<?php include 'frame.php'; ?>
<h3>
User Transactions
</h3>
<?php
	include 'logincode.php';
	include 'adminonly.php';
?>
<!-- This page MUST be called with the user ID in get parameters-->
Transactions for <?php 
				include 'openCon.php';
				$query = 'SELECT full_name FROM user WHERE id='.$_GET['userId'];
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				echo $row['full_name'];
				?>
<br/>
<table border=1 cellspacing=0>
	<tr><th>ID</th><th>Date</th><th>Amount</th><th>Order ID</th><th>Type</th></tr>
	<?php
		$query = '
SELECT trans.id,date_format(date,\'%d-%m-%Y\') date ,amount,order_id,type
FROM trans
WHERE
user_id = '.$_GET['userId'].'
			and trans.active = \'Y\'

ORDER BY trans.date';
		$result = mysql_query($query);
		$odd=true;
		while($row = mysql_fetch_array($result))
		{
			echo '<tr class="'.($odd?"odd":"even").'"><td>'.$row['id'].'</td><td>'.$row['date'].'</td><td>'.$row['amount'].'</td><td><a href="summary.php?orderId='.$row['order_id'].'">'.$row['order_id'].'</a></td><td>'.($row['type']=='C'?'Charge':'Order').'</td></tr>';
			$odd=!$odd;
		}	 
	?>
</table>

<?php include 'frameend.php'; ?>
</body>
</html>