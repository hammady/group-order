<html>
<?php
	if(isset($_GET['to'])) {
		$to      = $_GET['to'];
		$subject = $_GET['subject'];
		$message = $_GET['body'];
		$headers = 'From: OrderSystem <ordersystem@ordersystem.x10.mx>' . "\r\n" .
		    'Reply-To: ordersystem@ordersystem.x10.mx' . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();

		echo "Sending mail to $to with subject $subject and body $body and headers $headers";
		$stts = mail($to, $subject, $message, $headers) or die("Error occured");
//		$stts = mail(,,,'From: OrderSystem@gmail.com' ) ;
		if($stts)
			echo "Mail Sent";
		else
			echo "Error! Mail was not sent.";
	} 
?>
<body>
<form action="test3.php" method="get">
To:<input type="text" name="to"/>
<br/>
Subject:<input type="text" name="subject"/>
<br/>
Body:<input type="text" name="body"/>
<br/>
<input type="submit"/>
</form>
</body>
</html>