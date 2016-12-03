<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php';?>
<title>
Polls
</title>
</head>
<body>
<?php include 'frame.php';?>
<h3>
Polls
</h3>
<?php
	include 'logincode.php';
?>
<br/>
	<table cellspacing=0 border=1>
	<tr><th>ID</th><th>Title</th><th>Creator</th><th>Options</th></tr>
	<?php 
		include 'openCon.php';
		$query = 'SELECT p.id,p.title,u.full_name,p.state FROM poll p,user u WHERE p.owner_id = u.id ';
		if($_SESSION['userClass'] != 'O')
			$query .= "and state = 'O'";
		$result = mysql_query($query);
		$odd=true;
		while($row=mysql_fetch_array($result))
		{
			echo "<tr class=\"".($odd?"odd":"even")."\">
					<td>".$row['id']."</td>
					<td>".$row['title']."</td>
					<td>".$row['full_name']."</td>
					<td>";
			if($row['state'] == 'N' and $_SESSION['userClass'] == 'O')
				echo "<a href='newpoll.php?pollId=".$row['id']."'>Edit</a><br/>";
			else if($row['state'] == 'O')
				echo "<a href='vote.php?pollId=".$row['id']."'>Vote</a>";
			echo "	</td>
				</tr>";
			$odd = !$odd;
		}
	?>
	</table>
	<?php if($_SESSION['userClass'] == 'O')
	{?>
<h3>Create a new poll:</h3>
<form method=post action=newpoll.php>
	<div class="form">
		<ol>
			<li><label>Title:</label><input type=text name=title size=60 /></li>
			<li><label>Description:</label><textarea name=desc cols=46 rows=5></textarea></li>
			<li class="submit"><input type=submit name=newpoll value="Create new poll" /></li>
		</ol>
	</div>
</form>
<?php }?>
	<?php include 'frameend.php';?>
</body>
</html>