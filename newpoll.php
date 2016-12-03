<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php';?>
<title>
New Poll
</title>
</head>
<body>
<?php include 'frame.php';?>
<h3>
New Poll
</h3>
<?php
	include 'logincode.php';
	include 'owneronly.php';
?>
<?php
		if(isset($_POST['newpoll']) and $_SESSION['userClass'] == 'O')
		{
			$query = "INSERT INTO poll (title,description,owner_id,state) VALUES ('".mysql_real_escape_string($_POST['title'])."','".mysql_real_escape_string($_POST['desc'])."',".$_SESSION['userId'].",'N');";
			mysql_query($query);
			$query = 'SELECT last_insert_id()';
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			$pollId = $row[0];
			$_REQUEST['pollId'] = $row[0];
		} 
	$query = 'SELECT * FROM poll WHERE id='.$_REQUEST['pollId'];
	$result = mysql_query($query);
	$row = mysql_fetch_array($result); 	 
	if($row['state'] != 'N')
	{
		echo "Error: This poll is not editable.";
		include 'frameend.php';
		echo "</body>
		</html>";
		die(); 		
	}
		if(isset($_REQUEST['pollId']))
		{
			$pollId = mysql_real_escape_string($_REQUEST['pollId']);
		}
		if(isset($_POST['newoption']))
		{
			$query = "INSERT INTO poll_option (title,description,poll_id) VALUES ('".mysql_real_escape_string($_POST['title'])."','".mysql_real_escape_string($_POST['desc'])."',".mysql_real_escape_string($_REQUEST['pollId']).");";
			mysql_query($query);
		}

	?>
<?php
	$query = 'SELECT * FROM poll WHERE id='.$pollId;
	$result = mysql_query($query);
	$row = mysql_fetch_array($result); 

?>
Poll ID:<?php echo $row['id'];?>
<br/>
Title:<?php echo$row['title'];?>
<br/>
Description:<?php echo $row['description'];?>
<?php
	$query = "SELECT count(*) cnt FROM poll_option WHERE poll_id = ". $pollId;
	$result = mysql_query($query);
	$row = mysql_fetch_array($result); 
	if($row['cnt'] > 1)
	{ 
?>
<form method="post" action="vote.php?pollId=<?php echo $pollId;?>">
	<input type="submit" name="startPoll" value="Start & Vote"/>
</form>
<?php }?>

<h3>Poll Options:</h3>
<table cellspacing=0 border=1>
<tr><th>ID</th><th>Title</th><th>Description</th></tr>
<?php
	$query = 'SELECT * FROM poll_option WHERE poll_id = '.$pollId; 
	$result = mysql_query($query);
	$odd = true;
	while($row=mysql_fetch_array($result))
	{
		echo "<tr class=\"".($odd?"odd":"even")."\"><td>".$row['id']."</td><td>".$row['title']."</td><td>".$row['description']."</td></tr>";
		$odd = !$odd;
	}
?>
</table>
<h3>Add option:</h3>
<form method=post action=newpoll.php?pollId=<?php echo $pollId;?>>
	<div class="form">
		<ol>
			<li><label>Title:</label><input type=text name=title size=60 /></li>
			<li><label>Description:</label><textarea name=desc cols=46 rows=5></textarea></li>
			<li class="submit"><input type=submit name=newoption value="Add new option" /></li>
		</ol>
	</div>
</form>


<?php include 'frameend.php';?>
</body>
</html>