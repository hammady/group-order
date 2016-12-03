<div id='shopMenu'>
<table border=1 cellspacing=0>
<tr>
<th>Name</th><th>Description</th><th>Price (LE)</th><th>Notes</th><th>Quantity</th><?php //if($_SESSION['userClass'] == 'M') {echo "<th>Add As</th>";}?>
</tr>

<?php
	include 'openCon.php';
	//$shopId = $_REQUEST['shopId'];
	if(!isset($shopId))
		$shopId = $_REQUEST['shopId'];
	//$query = 'SELECT * FROM meal m where shop_id = '.$row['id'].';';
	//$query = "SELECT name,description,price,meal.id,ifnull(sum(count),0) total  FROM meal left join (select * from order_meal where owner_id = ".$_SESSION["userId"].") order_meal on meal.id = order_meal.meal_id where  shop_id = ".$_REQUEST["shopId"]." and name like '%" . $_REQUEST['query'] . "%' group by meal.id ORDER BY total DESC";
	$query = "SELECT * FROM meal WHERE shop_id = ". $shopId;
	if(isset($_REQUEST['query']))
		$query .= " and name like '%" . $_REQUEST['query'] . "%'";
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
							<input type=hidden id='meal_id' name='meal_id' value=".$row['id']." />
							<input type=button value='Add' onClick=\"fillAndSubmit(".$i."+1,'add','',".$row['id'].");\"/>
					
					</td>
			</tr>";
		$odd = ! $odd;
		$i++;
	}
?>
</table>
</div>