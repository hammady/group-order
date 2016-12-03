<SCRIPT type="text/javascript">
//<!-- Begin
function checkAll(field)
{
with(field)
{
for (i = 0; i < elements.length; i++)
	if(elements[i].name == 'users[]' || elements[i].name == 'groups')
		elements[i].checked = true ;
}
}
function uncheckAll(field)
{
with(field)
{
for (i = 0; i < elements.length; i++)
	if(elements[i].name == 'users[]' || elements[i].name == 'groups')
		elements[i].checked = false ;
}
}

function checkUser(id,check)
{
	x=id;
	with(document.orderData)
	{
		for (i = 0; i < elements.length; i++)
			if(elements[i].name == "users[]")
			{
				if(elements[i].value==x)
					elements[i].checked = check ;
			}
	}
}
function groupsClick(gname)
{
	with(document.orderData) 
	{
		for(i=0;i<groups.length; i++)
		{	
			if(groups[i].value==gname)
			{
				check = groups[i].checked;
			}
		}
	}
<?php
	include 'openCon.php';
	$query = 'SELECT DISTINCT ifnull(group_name,\'Other\') group_name FROM `user` u ORDER BY group_name;';
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		echo 'if(gname == \''.mysql_real_escape_string($row['group_name']).'\')
		{';
		$query = 'SELECT id FROM user WHERE group_name =\''.mysql_real_escape_string($row['group_name']).'\' or (\''.mysql_real_escape_string($row['group_name']).'\' = \'Other\' and group_name is null);';
		//echo $query;
		$result2 = mysql_query($query);
		while($row2 = mysql_fetch_array($result2))
		{
			echo 'checkUser("'.$row2['id'].'",check);';
		}
		echo '
		}';
	}
?>
}
function validateForm(form) {
	if(form.subject.value == '' ) {
		alert("You must enter subject for this order.");
		return false;
	}
	return true; 
}
//  End -->
</script>
Enter order data:
<br/>
<form action=neworder.php name=orderData method='post' onSubmit='return validateForm(this);'>
<div class="form">
<ol>
<!--	<li>
		<label>Order ID:</label>
		<input readonly type='text' name='orderId' value='<?php 
			include 'openCon.php';
			$result = mysql_query('SELECT ifnull(max(id) + 1,1) as orderId FROM foodorder;');
			$row = mysql_fetch_array($result);
			echo $row['orderId'];
			?>' />
	</li>-->
	<li>
		<label>Owner:</label>
		<input readonly type='text' name='owner' value="<?php echo $_SESSION['userfullname']; ?>" />
	</li>
	<li>
		<label>Subject:</label>
		<input type='text' name='subject' />
	</li>
	<li>
		<label>Date:</label>
		<input readonly type='text' name='cdate' value =<?php echo date('d-m-Y')?> />
	</li>
	<li>
		<label>Time:</label>
		<input readonly type='text' name='ctime' value =<?php 		//date_default_timezone_set('Africa/Cairo');
echo date('h:i:sA')?> />
	</li>
</ol>
</div>
Choose shop to order from:
<br/>
<?php
	include 'openCon.php';
	//viewing all shops
	$result = mysql_query("SELECT id,name,datediff(now() , ban_date) days,ban_date FROM shop WHERE banned <> 'Y';");
	$selected = false;
	while($row = mysql_fetch_array($result)) {
//		if(!isset($row['days']) or $row['days'] > 7)
//		{
			echo "<input type='radio' name='shop' value='".$row['id']."'";
			if (!$selected)
			{
				echo "checked='checked'";
				$selected = true;
			}
			echo " />";
//		}
		echo $row['name'];
//		if(!(!isset($row['days']) or $row['days'] > 7))
//		{
//			echo " is banned 7 days since ".$row['ban_date'].".";
//		}
		echo "<br/>";
	}
?>
Users who chose to receive messages will be notified.
<br/>
<input type="checkbox" checked= "checked" name="managed"/>Add managing money to this order.
<!-- 
<input type=button value='Select All' onclick="checkAll(document.orderData)" />
<input type=button value='Select None' onclick="uncheckAll(document.orderData)" />
<div id="notify">
<div id="personsToNotify">
Select people to notify:
<br/>
<?php
//	$query = 'SELECT * FROM user WHERE class <> \'N\' AND receiveWhenNewOrder = \'Y\';';
//	$result = mysql_query($query);
//	while($row = mysql_fetch_array($result))
//	{
//		echo "<input type=checkbox name='users[]' value=".$row['id']." />".$row['full_name']."<br/>";
//	}
	
?>
</div>
<div>
Or select groups to notify:
<br/>
<?php
//	$query = 'SELECT DISTINCT ifnull(group_name,\'Other\') group_name FROM `user` u WHERE class <> \'N\' ORDER BY group_name;';
//	$result = mysql_query($query);
//	while($row = mysql_fetch_array($result))
//	{
//		echo "
//					<input type=checkbox name='groups' value=\"".($row['group_name'])."\" onclick=\"groupsClick('".mysql_real_escape_string($row['group_name'])."')\" />
//					".$row['group_name']."
//			<br/>";
//	}
	
?>
</div> 
</div>-->
<div id="startOrder">
<input type='hidden' name = 'newOrder' value='entered' />
<input type='submit' value='Start order' />
</div>
</form>
