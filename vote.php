<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php';?>
<title>
Vote
</title>
<script type="text/javascript">
//<!--
function resize(){
//	 var oTextbox = this.document.getElementById("txt1");
//	 oTextbox.style = "height:200px;";
//	 oTextbox.cols = 100;
if (navigator.appName != "Microsoft Internet Explorer")
	document.getElementById("txt1").style.height = "0px";
document.getElementById("txt1").style.height = (document.getElementById("txt1").scrollHeight+20)+"px";
}
//-->
</script>
</head>
<body>
<?php include 'frame.php';?>
<h3>
Vote in poll <?php echo $_REQUEST['pollId'];?>
</h3>
<?php
	include 'logincode.php';
?>

<?php
function _exec($cmd)
{
   $WshShell = new COM("WScript.Shell");
   $oExec = $WshShell->Run($cmd, 0,false);
   //echo $cmd;
   return $oExec == 0 ? true : false;
}
	if(isset($_REQUEST['startPoll']) and $_SESSION['userClass'] == 'O')
	{
		$query = "UPDATE poll SET state='O' WHERE id =".$_REQUEST['pollId'];
		$result = mysql_query($query);
		$query = 'SELECT * FROM user WHERE class <> \'N\' AND receiveWhenNewOrder = \'Y\' and ip <> \'\' and ip is not null;';
		$result = mysql_query($query);
		$batch = fopen("c:\ip.bat","w");
		while($row = mysql_fetch_array($result))
		{
			$command = 'c:\ipmsg.exe /msg '.$row['ip'].' New poll has been created at Order System. Double click http://121.0.0.187:8080/vote.php?pollId='.$_REQUEST['pollId'].' to vote.';
			fprintf($batch,$command."\n");
		}
		fclose($batch);
		//echo $query;
		_exec("c:\ip.bat");
		
	}

	$query = 'SELECT * FROM poll WHERE id='.$_REQUEST['pollId'];
	$result = mysql_query($query);
	$row = mysql_fetch_array($result); 	 
	if($row['state'] != 'O')
	{
		echo "Error: This poll is not open for voting.";
		include 'frameend.php';
	?>
</body>
</html>
	<?php
		die(); 		
	}
	?>
<?php
	if(isset($_REQUEST['vote']))
	{
		$query = 'SELECT * FROM poll_option WHERE poll_id =' . $_REQUEST['pollId'];
		$result = mysql_query($query);
		if(!($row = mysql_fetch_array($result)))
		{
			die("This option is not in this poll! STOP HACKING OR YOU WILL BE BANNED!");
		}
		//Deleting previous records of vote
		$query = 'DELETE FROM poll_vote WHERE poll_id = '.$_REQUEST['pollId'].' and owner_id = '.$_SESSION['userId'];
		$result = mysql_query($query);
		
		//Inserting the new vote
		$query = 'INSERT INTO poll_vote (owner_id,poll_id,poll_option_id) VALUES ('.$_SESSION['userId'].','.$_REQUEST['pollId'].','.$_REQUEST['pollOptionId'].');';
		$result = mysql_query($query) or die(mysql_error());
	} 
	if(isset($_REQUEST['post']))
	{
		$query = "INSERT INTO poll_discuss (discuss, poll_id, owner_id, discuss_time) VALUES ('".mysql_real_escape_string($_REQUEST['discuss'])."', ".$_REQUEST['pollId'].", ".$_SESSION['userId'].", now())";
		$result = mysql_query($query);
	}
	if(isset($_REQUEST['deleteDiscuss']))
	{
		$query="SELECT owner_id FROM poll_discuss WHERE id=".$_REQUEST['discussId'];
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		if($row['owner_id'] == $_SESSION['userId'] or $_SESSION['userClass'] == 'O')
		{
			$query = "DELETE FROM poll_discuss WHERE id=".$_REQUEST['discussId'];
			$result = mysql_query($query);
		}
	}
	
?>
<?php
	$query = 'SELECT * FROM poll WHERE id = ' . $_REQUEST['pollId'];
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	echo $row['title'];
?>	
<br/>
Description:<?php echo $row['description']; ?>
<br/>
<form method=post>
<?php 
	$query = 'SELECT poll_option_id ID FROM poll_vote WHERE poll_id = '.$_REQUEST['pollId'].' and owner_id = '.$_SESSION['userId'];
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$optionId = $row['ID'];
	$query = 'SELECT * FROM poll_option WHERE poll_id = ' . $_REQUEST['pollId'];
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)) 
	{
?>
		<input type="radio" name="pollOptionId" <?php if($row['id'] == $optionId) echo 'checked="checked"';?> value="<?php echo $row['id'];?>"/><?php echo $row['title']?><br/><?php echo $row['description'];?>
<?php 		
	}
?>
<input type=submit name=vote value="Vote"/>
<input type=hidden name=pollId value="<?php echo $_REQUEST['pollId'];?>"/>
</form>
<?php if(isset($optionId))
{

?>
<h3>
Poll Results
</h3>
<table border=1 cellspacing="0">
	<tr><th>Option</th><th>Count</th><th>Percent</th><th>&nbsp;</th></tr>
	<?php
		$query = 'SELECT count(*) cnt
FROM poll_vote
WHERE poll_id = '.$_REQUEST['pollId'].'
GROUP BY poll_id';
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$votesCount = $row['cnt'];
		$query = 'SELECT o.title,o.description, count(v.id) cnt
FROM poll_option o
LEFT JOIN poll_vote v
ON o.poll_id = v.poll_id and v.poll_option_id = o.id
WHERE o.poll_id = '.$_REQUEST['pollId'].'
GROUP BY o.id'; 
		$result = mysql_query($query);
		$odd = true;
		while($row = mysql_fetch_array($result))
		{?>
			<tr class="<?php echo ($odd?"odd":"even");?>"><td><?php echo $row['title'];?></td><td><?php echo $row['cnt'];?></td><td><?php printf("%6.2f%%",$row['cnt']*100/$votesCount);?></td><td><img src="pollbar.php?percentage=<?php echo ($row['cnt']/$votesCount);?>" /></td></tr>
	<?php
		$odd=!$odd; 
		}
	?>
</table>
<?php }?>
<h3>Poll Discussion:</h3>
<form method="post"  action="vote.php?pollId=<?php echo $_REQUEST['pollId'];?>">
	<textarea id="txt1" rows="1" cols="50" style="height:38px;overflow: hidden;padding-top: 0px;padding-bottom: 0px" name=discuss onkeypress="resize()" onkeyup="resize()" onfocus="resize()"></textarea>
	<input type="submit" value="Post" name="post"/>
</form>
<table cellspacing="0">
<?php
	$query = "SELECT pd.id,u.id userId,u.full_name,pd.discuss,date_format(pd.discuss_time,'%d-%m-%Y %h:%i:%s %p') discuss_time ,date_format(pd.discuss_time,'%d-%m-%Y') discuss_date, date_format(now(),'%d-%m-%Y') today,date_format(pd.discuss_time,'%h:%i:%s %p') discuss_hour 
FROM poll_discuss pd,User u
WHERE pd.owner_id = u.id
and poll_id=" . $_REQUEST['pollId']
." ORDER BY pd.discuss_time ASC";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
?>
		<tr class="odd">
			<td>
				<b>
					<?php echo $row['full_name'];?>
					<br/>
					@ <?php if($row['discuss_date'] == $row['today']) echo $row['discuss_hour']; else echo $row['discuss_time'];?>
				</b>
			</td>
			<td><?php echo $row['discuss'];?></td>
			<td>
				<?php
					if($row['userId'] == $_SESSION['userId'] or $_SESSION['userClass'] == 'O')
					{ 
				?>
				<form method="post" action="vote.php?pollId=<?php echo $_REQUEST['pollId'];?>">
					<input type=submit value="Delete" name="deleteDiscuss"/>
					<input type=hidden name=discussId value="<?php echo $row['id'];?>" />
				</form>
				<?php
					} 
					else
					{ 
				?>
				&nbsp;
				<?php
					} 
				?>
				
			</td>
		</tr>
<?php 		
	} 
?>
</table>
<?php include 'frameend.php';?>
</body>
</html>