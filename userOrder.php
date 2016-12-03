<?php
	session_start();
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
	if (isset($_REQUEST['command']))
	{
		$query='SELECT * FROM order_meal WHERE order_id = '.$_REQUEST['orderId'].' and meal_id = '.$_REQUEST['meal_id'].' and owner_id ='.$_SESSION['userId'];
		if(isset($_REQUEST['notes']))
		{
			$query .= sprintf(' and notes = \'%s\';',mysql_real_escape_string($_REQUEST['notes']));
		}
		else
		{
			$query .= ';';
		}
		//echo $query;
		$result = mysql_query($query);
		if ($_REQUEST['command']=='add')
		{
			if(mysql_fetch_array($result)) //if there was an order meal with the same meal and order and owner, just increase the count else, insert a new record.
			{
				$query = sprintf('UPDATE order_meal SET count=count+'.$_REQUEST['count'].',notes=\'%s\' WHERE order_id = '.$_REQUEST['orderId'].' and meal_id = '.$_REQUEST['meal_id'].' and owner_id='.$_SESSION['userId'].' and notes = \'%s\';', mysql_real_escape_string(urldecode( $_REQUEST['notes'])), mysql_real_escape_string(urldecode( $_REQUEST['notes'])));
			}
			else
			{
				$query = 'SELECT shop_id FROM meal WHERE id = '.$_REQUEST['meal_id'] ;
				//echo $query;
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$mealShop = $row['shop_id'];
				$query = 'SELECT voted_shop_id FROM foodorder WHERE id = '.$_REQUEST['orderId'] ;
				//echo $query;
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$orderShop = $row['voted_shop_id'];
				if($mealShop == $orderShop and $_REQUEST['count'] > 0)
				{
					$query = sprintf('INSERT into order_meal (order_id,meal_id,owner_id,count,notes) VALUES ('.$_REQUEST['orderId'].','.$_REQUEST['meal_id'].','.$_SESSION['userId'].','.$_REQUEST['count'].',\'%s\');',mysql_real_escape_string(urldecode($_REQUEST['notes'])));
				}
			}
			//echo $query;
			$result = mysql_query($query);
		}
		else if($_REQUEST['command']=='minus')
		{
			$row=mysql_fetch_array($result);
			//echo $row['count']."<br/>";
			if($row['count'] > 1) // if count in DB > 1 then descrease the count in DB, else, delete the entire row.
			{
				$query = 'UPDATE order_meal SET count=count-1 WHERE order_id = '.$_REQUEST['orderId'].' and meal_id = '.$_REQUEST['meal_id'].' and owner_id='.$_SESSION['userId'].' and notes = \''.mysql_real_escape_string(urldecode($_REQUEST['notes'])).'\';';
			}
			else
			{
				$query = 'DELETE FROM order_meal WHERE order_id = '.$_REQUEST['orderId'].' and meal_id = '.$_REQUEST['meal_id'].' and owner_id='.$_SESSION['userId'].' and notes = \''.mysql_real_escape_string(urldecode($_REQUEST['notes'])).'\';';
			}
			//echo mysql_real_escape_string($_POST['notes']);
			//var_dump($_POST);
			//echo $query;
			$result = mysql_query($query);
		}
		else if($_REQUEST['command'] == 'insert')
		{
			//insert the meal
			$query = sprintf('
				INSERT INTO meal (name,description,price,shop_id,creator_id,type) 
				VALUE (\'%s\',\'%s\','.$_REQUEST['price'].','.$_REQUEST['shopId'].','.$_SESSION['userId'].',\''.$_REQUEST['type'].'\');'
				,mysql_real_escape_string($_REQUEST['name'])
				,mysql_real_escape_string($_REQUEST['description']));
			//echo $query;
			$result=mysql_query($query);
			//insert the order meal
			$query = 'SELECT LAST_INSERT_ID() id;';
			$result= mysql_query($query);
			$row = mysql_fetch_array($result);
			$mealId = $row[0];
			$query = sprintf(
								'INSERT into order_meal (order_id,meal_id,owner_id,count,notes) 
								VALUES ('.$_REQUEST['orderId'].','.$mealId.','.$_SESSION['userId'].','.$_REQUEST['count'].',\'%s\');'
				,mysql_real_escape_string(urldecode($_REQUEST['notes'])));
			$result = mysql_query($query);			 
		}
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
						<input type=hidden name=meal_id2 value=".$row['id']." />
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
