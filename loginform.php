<form method=post action=<?php echo substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);	
if(count($_GET) != 0)
		echo "?";
	$first = true;
	foreach ($_GET as $key => $value)
	{
		if(!$first)
			echo '&';
		echo $key .'='.$value;
		$first = false;
	} 
 ?>>
<div class="form">
<ol>
	<li>
		<label>Username:</label>
		<input type=text name='user' />
	</li>
	<li>
		<label>Password:</label>
		<input type=password name=pass />
	</li>
	<li class="submit" >
		<input type=submit value='Log in' />
	</li>
</ol>
</div>
</form>
<br />
Forgot your username or password?
<br/>
Order System will send you your username and password reset code. 
<br/>
Using this code you will be able to set a new password. Please enter your IP here:
<form method=post action=<?php echo substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);	
if(count($_GET) != 0)
		echo "?";
	$first = true;
	foreach ($_GET as $key => $value)
	{
		if(!$first)
			echo '&';
		echo $key .'='.$value;
		$first = false;
	} 
?>>
<div class="form">
<ol>
	<li>
		<label>IP:</label>
		<input type=text name='ip' />
	</li>
	<li class="submit">
		<input type=submit name=sendResetCode value='Send me my data' />
	</li>
</ol>
</div>
</form>
<br />
Already received your password reset code? Reset your password here:
<form method=POST>
<div class="form" onSubmit="return validate(this)">
<ol>
	<li>
		<label>Reset code:</label>
		<input type=text name='resetCode' />
	</li>
	<li>
		<label>Username:</label>
		<input type=text name='username' />
	</li>
	<li>
		<label>New Password:</label>
		<input type=password name='newpass' />
	</li>
	<li>
		<label>Confirm Password:</label>
		<input type=password name='newpass2' />
	</li>
	<li class="submit">
		<input type=submit name=resetPassword value='Reset Password' />
	</li>
</ol> 
</div>
</form>
Don't have an account? <a href=signup.php>Sign up here.</a>