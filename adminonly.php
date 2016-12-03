<?php 
	if(!isset($_SESSION['userClass']) or ($_SESSION['userClass']!='M' and $_SESSION['userClass'] != 'O'))
	{
		echo "You are not authorized to see this page!";
		include "frameend.php";
		echo "</body></html>";
		die();
	}
?>