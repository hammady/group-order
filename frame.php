<?php 
echo '<div id="wrapper">';
echo '<div id="top">';
echo '</div>';
echo '<div id="content">';
echo '<div id="header">';
echo 'Order System';
echo '</div>';
//echo '<marquee><a href="balancesIntro.php">Important Notice: Users Balances added</a></marquee>';
echo '<div id="menu">';
echo '	<ul>';
echo '		<li><a href="index.php">Home</a></li>';
echo '		<li><a href="neworder.php">Create new order</a></li>';
echo '		<li><a href="options.php">My Options</a></li>';
echo '		<li><a href="polls.php">Polls</a></li>';
echo '		<li><a href="shops.php">View Menus</a></li>';
echo '		<li><a href="admin.php">Administration</a></li>';
echo '		<li><a href="stats.php">Statistics</a></li>';
echo '	</ul>';
echo '</div>';
echo '<div id="stuff">';

function sendmail($to, $subject, $message) {
	$headers = 'From: OrderSystem <ordersystem@ordersystem.x10.mx>' . "\r\n" .
    'Reply-To: ordersystem@ordersystem.x10.mx' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

	//echo "Sending mail to $to with subject $subject and body $body and headers $headers";
	try {
		//echo "Sending email to $to with subject $subject body $message";
		$stts = mail($to, $subject, wordwrap($message,70), $headers);// or die("Error occured");
		return $stts;	
	} catch (Exception $e) {
		echo $e->getMessage();
	}
	
	
}
?>
