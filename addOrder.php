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
		url=url+"?notes="+encodeURI(document.getElementsByName('notes')[index].value);
		url=url+"&count="+document.getElementsByName('count')[index].value;
		url=url+"&meal_id="+mealId;
		url=url+"&orderId="+<?php echo $_REQUEST['orderId'];?>;
		url=url+"&command="+command;
		url=url+"&sid="+Math.random();
	} else if (command = "minus") {
		url="userOrder.php";
		url=url+"?notes="+encodeURI(notes);
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
	document.getElementById("searchQuery").focus();
//	document.actual.notes.value= document.visible.notes[index].value;
//	document.actual.count.value= document.visible.count[index].value;
//	document.actual.meal_id.value= document.visible.meal_id[index].value;
//	document.actual.submit();
}


	function stateChanged2() 
	{ 
	if (xmlHttp2.readyState==4)
	{ 
	document.getElementById("menu").innerHTML=xmlHttp2.responseText;
	}
	}

	
	function search(shopId) {
		var url;
		var query;
		query = (document.getElementById('searchQuery').value);
		url = "menuresults.php?query=" + query + "&shopId=" + shopId + "&sid=" + Math.random();
		xmlHttp2=GetXmlHttpObject();
		if (xmlHttp2==null)
		  {
		  alert ("Your browser does not support AJAX!");
		  return;
		  } 
			
		xmlHttp2.onreadystatechange=function() 
		{ 
			if (xmlHttp2.readyState==4)
			{ 
			document.getElementById("shopMenu").innerHTML=xmlHttp2.responseText;
			document.getElementById("searchQuery").focus();
			}
		}
		xmlHttp2.open("GET",url,true);
		xmlHttp2.send(null);
//		document.actual.notes.value= document.visible.notes[index].value;
//		document.actual.count.value= document.visible.count[index].value;
//		document.actual.meal_id.value= document.visible.meal_id[index].value;
//		document.actual.submit();
	}
</script>
</head>
<body OnLoad='document.getElementById("searchQuery").focus();'>
<?php include 'frame.php';?>
<?php include 'checkid.php'; ?>
<h3>
Manage order
</h3>
<?php
	include 'logincode.php';
?>
<br/>
<?php
	if(isset($_POST['agree']) && $_POST['agree']=='Y')
	{
		$_SESSION['seenNote1'] = 'Y';
		include "openCon.php";
		$query = "UPDATE user SET seenNote1='Y' WHERE id=".$_SESSION['userId'].";";
		$result = mysql_query($query);
	}
	if($_SESSION['seenNote1']!='Y')
	{
		include "note1.php";
		include "frameend.php";
		echo "</body></html>";
		die();
	}
	if(isset($_REQUEST['managed']))
	{
		if($_SESSION['userClass'] == 'M' or $_SESSION['userClass'] == 'O')
		{
			if($_REQUEST['managed'] == 'no') //Set managed = 'N'
			{
				$query = "UPDATE foodorder SET managed = 'N' WHERE id = " . $_REQUEST['orderId'];
				$result = mysql_query($query); 
			}
			else if($_REQUEST['managed']=='yes')
			{
				$query = "UPDATE foodorder SET managed = 'Y' WHERE id = " . $_REQUEST['orderId'];
				$result = mysql_query($query); 
			}
		}
	}
?>
This order is from <?php $query = 'SELECT SHOP.name, foodorder.managed FROM SHOP,FOODORDER WHERE foodorder.voted_shop_id = shop.id and foodorder.id = '.$_REQUEST['orderId'];
//echo $query;
$result = mysql_query($query);
$row = mysql_fetch_array($result);
echo $row['name'];
?>
<br/>
<a href='progress.php?orderId=<?php echo $_REQUEST['orderId']?>'>View order progress</a>
<br/>
<?php 
	if($_SESSION['userClass'] == 'M' or $_SESSION['userClass'] == 'O')
	{
		if($row['managed'] == 'Y')
		{
			echo "Management money will be added to this order";
			echo "<br/>";
			echo "<a href='addOrder.php?orderId=".$_REQUEST['orderId']."&managed=no'>Remove management money</a>";
			echo "<br/>";
		}
		else
		{
			echo "Management money will NOT be added to this order";
			echo "<br/>";
			echo "<a href='addOrder.php?orderId=".$_REQUEST['orderId']."&managed=yes'>Add management money</a>";
			echo "<br/>";
		}
 		echo "Note: A moderator <b>MUST</b> close the order after you finish to show the summary page.
	<br/>
	<a href='close.php?orderId=".$_REQUEST['orderId']."'>Close order</a>";
	}
?>
<?php 
	include 'userOrder.php';
?>
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

	<form>
		<td>Search for a meal by name:</td>
		<td><input type=text id="searchQuery" name=searchQuery onkeyup="search(<?php echo $row['id'];?>);" /></td>
		<td></td>
		<td></td>
		<td></td>
	</form>

<?php
	$shopId = $row['id'];
//	//$query = 'SELECT * FROM meal m where shop_id = '.$row['id'].';';
//	$query = 'SELECT name,description,price,meal.id,ifnull(sum(count),0) total  FROM meal left join (select * from order_meal where owner_id = '.$_SESSION['userId'].') order_meal on meal.id = order_meal.meal_id where  shop_id = '.$row['id'].' group by meal.id ORDER BY total DESC';
//	$result = mysql_query($query);
//	$odd = true;
//	$i=0;
//	while ($row = mysql_fetch_array($result))
//	{
//		echo "<tr class=\"".($odd?"odd":"even")."\">
//					<td>
//						".htmlentities($row['name'],ENT_COMPAT,'UTF-8')."
//					</td>
//					<td>
//						".$row['description']."&nbsp;
//					</td>
//					<td>
//						".$row['price']."
//					</td>
//					<td>
//							<input type=text name=notes />
//					</td>
//					<td>
//							<input type=text name=count  size=1 value=1 />
//							<input type=hidden name=command value=add />
//							<input type=hidden name=meal_id value=".$row['id']." />
//							<input type=button value='Add' onClick=\"fillAndSubmit(".$i.",'add');\"/>
//					
//					</td>
//			</tr>";
//		$odd = ! $odd;
//		$i++;
//	}
	include 'menuresults.php';
?>



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
