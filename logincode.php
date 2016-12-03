<?php
	include 'openCon.php';
	if (! isset($_SESSION['loggedin']) or $_SESSION['loggedin'] == false)
	{
		if(isset($_POST['user']))
		{
			$query = 'SELECT * FROM user WHERE name=\''.mysql_real_escape_string($_POST['user']).'\' and password = password(\''.mysql_real_escape_string($_POST['pass']).'\') and class<>\'N\';';
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			if (isset($row['id']))
			{
				$_SESSION['loggedin'] = true;
				$_SESSION['userfullname'] = $row['full_name'];
				$_SESSION['username'] = $row['name'];
				$_SESSION['userId'] = $row['id'];
				$_SESSION['userClass'] = $row['class'];
				$_SESSION['ordersPerPage'] = $row['ordersPerPage'];
				$_SESSION['seenNote1'] = $row['seenNote1'];
				$_SESSION['balance'] = $row['balance'];
				echo "Welcome, ". $_SESSION['userfullname'] .' <a href=logout.php> Log out</a><br/>';
			}
			else
			{
				echo "Incorrect username or password. Please try to log in again.";
				include 'loginform.php';
			include 'frameend.php';
			echo '</body></html>';
			die();
			}
		}
		else if(isset($_POST['ip']))
		{
			//Use this code to generate a new password and insert its hash to db then send it to user
			/*
			 * substr(str_shuffle("abcefghijklmnopqrstuvwxyz" . "ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 16)
			 */
			//Select the password of the user that has this IP
			$query = 'SELECT * FROM user WHERE ip=\''.$_POST['ip'].'\';';
			$result=mysql_query($query);
			$row=mysql_fetch_array($result);
			if($row)
			{
				$generatedCode = substr(str_shuffle("abcefghijklmnopqrstuvwxyz01234" . "ABCDEFGHIJKLMNOPQRSTUVWXYZ56789"), 0, 16);
				$query = 'UPDATE user SET passwordResetCode = \''.$generatedCode.'\' WHERE ip=\''.$_POST['ip'].'\'';
				$result=mysql_query($query);
				$command = 'c:\ipmsg.exe /msg /seal '.$row['ip'].' Your username is "'.$row['name'].'". Your password reset code is "'.$generatedCode.'".';
				exec($command);
				echo 'Your data has been sent. If you remember your password you can log in directly. You can also enter password reset code to reset your password'; 
			}
			else
			{
				echo 'Unable to find this IP. Please enter another IP or contact the administrator.';
			}
			include 'loginform.php';
			include 'frameend.php';
			echo '</body></html>';
			die();
			
		}
		else if(isset($_POST['resetCode']))
		{
			$query = 'SELECT id FROM user WHERE name = \''.$_POST['username'].'\' and passwordResetCode = \''.$_POST['resetCode'].'\'';
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			if($row)
			{
				$query = "UPDATE user SET password=password('".$_POST['newpass']."') WHERE id = ".$row['id'];
				$result = mysql_query($query);
				echo "Your password was successfully changed. Please log in using the new password.";
			}
			else
			{
				echo "Incorrect username or password reset code. Your password was not reset.";
			}
			include 'loginform.php';
			include 'frameend.php';
			echo '</body></html>';
			die();
		}
		else
		{
			echo "Please log in.<br/>";
			include 'loginform.php';
			include 'frameend.php';
			echo '</body></html>';
			die();
		}
	}
	else
	{
		echo "Welcome, " . $_SESSION['userfullname'] .' <a href=logout.php> Log out</a><br/>';
	}
?>