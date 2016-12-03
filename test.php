<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php';?>
<title>
Manage order
</title>
<script type="text/javascript">
function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}
function stateChanged() 
{ 
if (xmlHttp.readyState==4)
{ 
document.getElementById("userOrder").innerHTML=xmlHttp.responseText;
}
}
function fillAndSubmit(index,command,notes,mealId) {
	var url;
	if(command == "add") {
		url="userOrder.php";
		url=url+"?notes="+document.visible.notes[index].value;
		url=url+"&count="+document.visible.count[index].value;
		url=url+"&meal_id="+document.visible.meal_id[index].value;
		url=url+"&orderId="+<?php echo $_REQUEST['orderId'];?>;
		url=url+"&command="+command;
		url=url+"&sid="+Math.random();
	} else if (command = "minus") {
		url="userOrder.php";
		url=url+"?notes="+notes;
		url=url+"&meal_id="+mealId;
		url=url+"&orderId="+<?php echo $_REQUEST['orderId'];?>;
		url=url+"&command="+command;
		url=url+"&sid="+Math.random();
	}
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	  {
	  alert ("Your browser does not support AJAX!");
	  return;
	  } 
		
	xmlHttp.onreadystatechange=function() 
	{ 
		if (xmlHttp.readyState==4)
		{ 
		document.getElementById("userOrder").innerHTML=xmlHttp.responseText;
		}
	}
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
//	document.actual.notes.value= document.visible.notes[index].value;
//	document.actual.count.value= document.visible.count[index].value;
//	document.actual.meal_id.value= document.visible.meal_id[index].value;
//	document.actual.submit();
}
</script>
</head>
<body>
<?php include 'frame.php';?>
<?php include 'checkid.php'; ?>
<h3>
Manage order
</h3>
<?php
	include 'logincode.php';
?>
<!--First adding order in database-->
<?php
	include 'openCon.php';
	$query = 'SELECT state FROM foodorder WHERE id = '.$_REQUEST['orderId'].';';
	//echo $query;
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if ($row['state'] == 'X' or $row['state'] == 'H')
	{
		die('Order has been closed!');
	}
	if (isset($_POST['command']))
	{
		$query='SELECT * FROM order_meal WHERE order_id = '.$_REQUEST['orderId'].' and meal_id = '.$_POST['meal_id'].' and owner_id ='.$_SESSION['userId'];
		if(isset($_POST['notes']))
		{
			$query .= sprintf(' and notes = \'%s\';',mysql_real_escape_string($_POST['notes']));
		}
		else
		{
			$query .= ';';
		}
		//echo $query;
		$result = mysql_query($query);
		if ($_POST['command']=='add')
		{
			if(mysql_fetch_array($result)) //if there was an order meal with the same meal and order and owner, just increase the count else, insert a new record.
			{
				$query = sprintf('UPDATE order_meal SET count=count+'.$_POST['count'].',notes=\'%s\' WHERE order_id = '.$_REQUEST['orderId'].' and meal_id = '.$_POST['meal_id'].' and owner_id='.$_SESSION['userId'].' and notes = \'%s\';',mysql_real_escape_string($_POST['notes']),mysql_real_escape_string($_POST['notes']));
			}
			else
			{
				$query = sprintf('INSERT into order_meal (order_id,meal_id,owner_id,count,notes) VALUES ('.$_REQUEST['orderId'].','.$_POST['meal_id'].','.$_SESSION['userId'].','.$_POST['count'].',\'%s\');',mysql_real_escape_string($_POST['notes']));
			}
			//echo $query;
			$result = mysql_query($query);
		}
		else if($_POST['command']=='minus')
		{
			$row=mysql_fetch_array($result);
			//echo $row['count']."<br/>";
			if($row['count'] > 1) // if count in DB > 1 then descrease the count in DB, else, delete the entire row.
			{
				$query = 'UPDATE order_meal SET count=count-1 WHERE order_id = '.$_REQUEST['orderId'].' and meal_id = '.$_POST['meal_id'].' and owner_id='.$_SESSION['userId'].' and notes = \''.mysql_real_escape_string($_POST['notes']).'\';';
			}
			else
			{
				$query = 'DELETE FROM order_meal WHERE order_id = '.$_REQUEST['orderId'].' and meal_id = '.$_POST['meal_id'].' and owner_id='.$_SESSION['userId'].' and notes = \''.mysql_real_escape_string($_POST['notes']).'\';';
			}
			//echo mysql_real_escape_string($_POST['notes']);
			//var_dump($_POST);
			//echo $query;
			$result = mysql_query($query);
		}
		else if($_POST['command'] == 'insert')
		{
			//insert the meal
			$query = sprintf('
				INSERT INTO meal (name,description,price,shop_id) 
				VALUE (\'%s\',\'%s\','.$_POST['price'].','.$_POST['shopId'].');'
				,mysql_real_escape_string($_POST['name'])
				,mysql_real_escape_string($_POST['description']));
			$result=mysql_query($query);
			//insert the order meal
			$query = 'SELECT LAST_INSERT_ID() id;';
			$result= mysql_query($query);
			$row = mysql_fetch_array($result);
			$mealId = $row[0];
			$query = sprintf(
								'INSERT into order_meal (order_id,meal_id,owner_id,count,notes) 
								VALUES ('.$_REQUEST['orderId'].','.$mealId.','.$_SESSION['userId'].','.$_POST['count'].',\'%s\');'
				,mysql_real_escape_string($_POST['notes']));
			$result = mysql_query($query);			 
		}
	}
	
?>
<br/>
This order is from <?php $query = 'SELECT SHOP.name FROM SHOP,FOODORDER WHERE foodorder.voted_shop_id = shop.id and foodorder.id = '.$_REQUEST['orderId'];
//echo $query;
$result = mysql_query($query);
$row = mysql_fetch_array($result);
echo $row['name'];
?>
<br/>
<a href='progress.php?orderId=<?php echo $_REQUEST['orderId']?>'>View order progress</a>
<br/>
<?php 
	$query = 'SELECT owner_id FROM foodorder WHERE id = '.$_REQUEST['orderId'].';';
	//echo $query;
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if($row['owner_id'] == $_SESSION['userId'])
	{
 echo "<a href='close.php?orderId=".$_REQUEST['orderId']."'>Close order</a>
	<br/>
	Note: You <b>MUST</b> close the order after you finish to show the summary page.";
	}
?>
<!--Second viewing current order-->
<div id="userOrder">
<h4>Your order:</h4>
<table border=1 cellspacing=0>
<tr>
<th>Name</th><th>Notes</th><th>Description</th><th>Unit Price (LE)</th><th>Quantity</th><th>Total Price (LE)</th>
</tr>
<?php
	include 'openCon.php';
	$query = 'SELECT meal.id,meal.name,meal.description,meal.price, m.count,m.notes FROM order_meal m, meal where meal.id = m.meal_id and order_id = '.$_REQUEST['orderId'].' and owner_id = '.$_SESSION['userId'].';';
	//echo $query;
	$result = mysql_query($query) or die(mysql_error());
	$odd = true;
	while ($row = mysql_fetch_array($result))
	{
		echo "<tr class=\"".($odd?"odd":"even")."\">
				<td>
					".($row['name'])."
				</td>
				<td>
					".$row['notes']."
				</td>
				<td>
					".$row['description']."
				</td>
				<td>
					".$row['price']."
				</td>
				<td>
					<form method=post action=addOrder.php?orderId=".$_REQUEST['orderId'].">
						
						".$row['count']."
						<input type=button name=minusOne value='-1' onClick='fillAndSubmit(0,\"minus\",\"".htmlspecialchars($row['notes'],ENT_QUOTES)."\",".$row['id'].");' />
						<input type=hidden name=command value=minus />
						<input type=hidden name=meal_id value=".$row['id']." />
						<input type=hidden name=notes value='".htmlspecialchars($row['notes'],ENT_QUOTES)."' />
					</form>
				</td>
				<td align=right>
					".$row['price']*$row['count']."
				</td>
			</tr>
			";
		$odd = !$odd;
	}
?>
	<tfoot>
		<tr>
			<td><b>Total (without delivery)</b></td>
			<td colspan=5 align=right> <b>
				<?php
					$query='SELECT ifnull(sum(price*count),0) total FROM order_meal m, meal where meal.id = m.meal_id and order_id = '.$_REQUEST['orderId'].' and owner_id = '.$_SESSION['userId'].';';
					$result=mysql_query($query);
					$row=mysql_fetch_array($result);
					echo $row['total'].'';
				?></b>
			</td>
		</tr>
	</tfoot>
</table>
</div>
<!--Third display all meals-->
<br/>
<h4>
<?php
	include 'openCon.php';
	//$query = 'select vote.order_id,choice_id,count(*) cnt,shop.name shop_name from vote,shop_choice,shop where shop.id = shop_choice.shop_id and vote.choice_id = shop_choice.id and vote.order_id = '.$_REQUEST['orderId'].' group by order_id,choice_id having max(cnt)';
	$query = 'SELECT shop.id id, shop.name name FROM shop,foodorder WHERE foodorder.voted_shop_id = shop.id and foodorder.id='.$_REQUEST['orderId'].';';
	//echo '<br/>'.$query;
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	echo $row['name']." menu:";
?>
</h4>
<form name="actual" action="addOrder.php?orderId=<?php echo $_REQUEST['orderId']; ?>" method=post>
	<input type=hidden name=notes />
	<input type=hidden name=count  size=6 value=1 />
	<input type=hidden name=command value=add />
	<input type=hidden name=meal_id />
</form>
<form name="visible" method=post action=addOrder.php?orderId=<?php $_REQUEST['orderId'];?> >
<table border=1 cellspacing=0>
<tr>
<th>Name</th><th>Description</th><th>Price (LE)</th><th>Notes</th><th>Quantity</th>
</tr>
<?php
	$shopId = $row['id'];
	$query = 'SELECT * FROM meal m where shop_id = '.$row['id'].';';
	$result = mysql_query($query);
	$odd = true;
	$i=0;
	while ($row = mysql_fetch_array($result))
	{
		echo "<tr class=\"".($odd?"odd":"even")."\">
					<td>
						".htmlentities($row['name'],ENT_COMPAT,'UTF-8')."
					</td>
					<td>
						".$row['description']."&nbsp;
					</td>
					<td>
						".$row['price']."
					</td>
					<td>
							<input type=text name=notes />
					</td>
					<td>
							<input type=text name=count  size=1 value=1 />
							<input type=hidden name=command value=add />
							<input type=hidden name=meal_id value=".$row['id']." />
							<input type=button value='Add' onClick=\"fillAndSubmit(".$i.",'add');\"/>
					
					</td>
			</tr>";
		$odd = ! $odd;
		$i++;
	}
?>
</table>
</form>
<!-- Fourth: Insert new meal and add it to order -->
<?php
//	if($_SESSION['userClass']=='M')
	{
		include 'addMeal.php';
	}
?>
<?php include 'frameend.php';?>
</body>
</html>