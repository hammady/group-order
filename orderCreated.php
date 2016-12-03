<?php
function _exec($cmd)
{
   $WshShell = new COM("WScript.Shell");
   $oExec = $WshShell->Run($cmd, 0,false);
   //echo $cmd;
   return $oExec == 0 ? true : false;
}
	include 'openCon.php';
	$query = 'SELECT datediff(now() , ban_date) days,banned FROM shop WHERE id ='.$_POST['orderId'];
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if($row['banned'] == 'Y')
	{
		die("This shop is banned. STOP hacking or you too will be banned!");
	}
	$query = "INSERT INTO foodorder (subject,date_created,state,owner_id,voted_shop_id,managed) values ('".mysql_real_escape_string($_POST['subject'])."','".date_format(new DateTime($_POST['cdate'].' '.$_POST['ctime']),'Y-n-j H:i:s')."','C','".$_SESSION['userId']."',".$_POST['shop'].",".($_POST['managed']=='on'?'\'Y\'':'\'N\'').");";
//	echo "<br/>".$_POST['orderId']."<br/>";
//	echo "<br/>".$_POST['owner']."<br/>";
//	echo "<br/>".$query."<br/>";
	mysql_query($query) or die(mysql_error());

	$query = 'SELECT LAST_INSERT_ID() id;';
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$orderId = $row['id'];
	$query = 'SELECT name FROM shop WHERE id='.$_POST['shop'];
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$shopName = $row['name'];
	$query = 'SELECT email FROM user WHERE class <> \'N\' AND receiveWhenNewOrder = \'Y\';';
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		$stts = sendmail($row['email'], 'New Order', 'New order has been created from '.$shopName. '. Click here http://ordersystem.x10.mx/addOrder.php?orderId='.$orderId.' to manage your order.');
	}
	echo "Your order has been started.";
?>
<br/>
<a href="addOrder.php?orderId=<?php echo $orderId;?>">Manage order</a>
