<?php
	$query = 'SELECT * FROM menu WHERE shop_id = '.$shopId.' ORDER BY number';
	$result = mysql_query($query);
	while($row=mysql_fetch_array($result))
	{
		echo '<a target="_blank" href="'.$row['path'].'"> <img class="shopMenu" src="'.$row['path'].'" /></a>';
	}
	
?>
<p>If you did not find your meal above, you can insert it here.</p><p> PS: Meals inserted here will be added to your order too.</p>
<p>Do NOT add false meals. Abusers will be banned!</p>
<form method=post action=addOrder.php?orderId=<?php echo $_REQUEST['orderId'];?>>
	<input type=hidden name=command value=insert />
	<input type=hidden name=shopId value=<?php echo $shopId; ?> />
	<input type=hidden name='count' size=6 value=1 />
	<div class="form">
	<ol>
		<li>
			<label>Name:</label><input type=text name='name' size = 15 />
		</li>
		<li>
			<label>Description:</label><input type=text name='description' size=20 />
		</li>
		<li>
			<label>Price:</label><input type=text name='price' size=6 />
		</li>
		<li>
			<label>Notes:</label><input type=text name='notes' />
		</li>
		<li>
			<label>Type:</label><input type=radio name='type' value='S' checked=true />Static
		<input type=radio name='type' value='D' />Dynamic
		</li>
		<li class="submit">
			<input type=submit value='Insert &amp; Add' />
		</li>
	</ol>
	</div>
</form>
