<?php
	session_start();
?>
<html>
<head>
<meta http-equiv="REFRESH" content="1;url=index.php">
<title>
Log out
</title>
</head>
<body>
<h3>
Log out
</h3>
<?php
	if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Finally, destroy the session.
session_destroy();
?>
You have been logged out.
<br/>
<a href=index.php>Home</a>
</body>
</html>