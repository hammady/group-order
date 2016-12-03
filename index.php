<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php'; ?>
<title>
Order System
</title>
</head>
<body>
<?php include 'frame.php'; ?>

<?php
	if(isset($_SESSION['userId']))
	{
		echo "Welcome, ".$_SESSION['userfullname'] .' <a href=logout.php> Log out</a>';
	}
	else
	{
		echo "Welcome, Guest. Please <a href='login.php'>Log in</a> or <a href='signup.php'>Sign up</a>.";
	}
?>
<br/>
This is a list of open orders.
<br/>
<table border=1 cellspacing=0>
<tr><th>No.</th><th>Subject</th><th>Shop</th><th>Owner</th><th>Date</th><th>Time</th><th>State</th><th>Options</th></tr>
<?php
	include 'openCon.php';
	$query = 'SELECT foodorder.id order_id,foodorder.subject,shop.name,date_format(foodorder.date_created,\'%d-%m-%Y\') date_created,date_format(foodorder.date_created,\'%h:%i:%s%p\') time_created,foodorder.state,user.full_name owner,user.id userId FROM foodorder,shop,user WHERE foodorder.voted_shop_id = shop.id and foodorder.owner_id = user.id and foodorder.state <> \'H\' ORDER BY foodorder.id desc ';
	if(isset($_GET['pageNum']))
	{
		$pageNum = $_GET['pageNum'];
	}
	else
	{
		$pageNum = 1;
	}
	if(isset($_SESSION['ordersPerPage']))
		 $ordersPerPage = $_SESSION['ordersPerPage'];
	 else
		 $ordersPerPage = 20;
	 $fromOrder = ($pageNum - 1)*$ordersPerPage;
	 $query .= 'LIMIT ' . $fromOrder . ',' . $ordersPerPage . ';';
	//echo $query;
	$result = mysql_query($query);
	$odd=true;
	while ($row = mysql_fetch_array($result)) 
	{
		switch($row['state']) {
			case 'C':
			$state = 'Open';
			$options = '<a href="addOrder.php?orderId='.$row['order_id'].'">Manage</a><br/>';
			$options .= '
			<a href="progress.php?orderId='.$row['order_id'].'">Progress</a><br/>';
			if($_SESSION['userClass'] == 'M' || $_SESSION['userClass'] == 'O')
				$options .= '
				<a href="close.php?orderId='.$row['order_id'].'">Close</a><br/>';
			break;
			case 'X':
			$state = 'Closed';
			$options = '<a href="summary.php?orderId='.$row['order_id'].'">Summary</a><br/>';
			if($_SESSION['userClass'] == 'M' || $_SESSION['userClass'] == 'O')
			{
				$options.= '
				<a href="hide.php?orderId='.$row['order_id'].'">Hide</a><br/>';
				$options.= '
				<a href="delete.php?orderId='.$row['order_id'].'">Delete</a><br/>';
				$options.= '
				<a href="reopen.php?orderId='.$row['order_id'].'">Reopen</a><br/>';
			}
			if ($_SESSION['userClass'] == 'O') 
			{
				$options.= '
				<a href="trans.php?orderId='.$row['order_id'].'">Transactions</a><br/>';
			}
			break;
			}
		echo "
			<tr class=\"".($odd?"odd":"even")."\">
				<td>".$row['order_id']."</td>
				<td>".(($row['subject']!="")?$row['subject']:"&nbsp;")."</td>
				<td>".$row['name']."</td>
				<td>".$row['owner']."</td>
				<td>".$row['date_created']."</td>
				<td>".$row['time_created']."</td>
				<td>".$state."</td>
				<td>".$options."</td>
			</tr>";
		$odd=! $odd;
	}
	echo "";
?>
</table>
<?php
	//get number of pages
	$query = 'SELECT count(*) /'.$ordersPerPage.' pageCount FROM foodorder WHERE state <> \'H\';';
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if($pageNum == 1)
	{
		echo "< Previous";
	}
	else
	{
		echo '<a href="index.php?pageNum='.($pageNum-1).'">< Previous</a>';
	}
	echo ' ';
	if($pageNum == ceil($row['pageCount']))
	{
		echo "Next >";
	}
	else
	{
		echo '<a href="index.php?pageNum='.($pageNum+1).'">Next ></a>';
	}
?>
<?php include 'frameend.php';?>
</body>

</html>