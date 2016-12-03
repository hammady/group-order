<html>
<?php
	if(isset($_POST['send']))
	{
		mail($_POST['to'],$_POST['subject'],$_POST['message'],'From: Order System');
	} 
?>
<body>
<form method="post">
To:<input type="text" name="to"/>
<br/>
Subject:<input type="text" name="subject"/>
<br/>
Message:<input type="text" name="message"/>
<br/>
<input type="submit" name="send" value="Send"/>

</form>
</body>
</html>