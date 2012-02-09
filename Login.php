<?php
session_start();
require_once 'backendfiles/dbconfig.php';
$error = false;
$failedLoggIn = false;
if(isset($_SESSION['ProfileID']))
{
	$UserName = $_SESSION['UserName'];
	$ProfileID = $_SESSION['ProfileID'];
}
else
{
	$UserName = 'Guest';
	$ProfileID = 0;
}
$loggingIn = false;
if(isset($_POST['login']))
{
	$userName = $_POST['user'];
	$query = 'SELECT ProfileID FROM Profile WHERE UserName="'.$_POST['user'].'" AND Password="'.$_POST['pass'].'"';
	$result = mysql_query($query, $dblink) or die("Retrieve query failed: $query ".mysql_error());
	if($line = mysql_fetch_assoc($result))
	{
		$query = 'UPDATE Profile SET FailedPass=0, LastLoggedIn=CURDATE() WHERE UserName="'.$_POST['user'].'"';
		mysql_query($query, $dblink) or die("Retrieve query failed: $query ".mysql_error());
		$_SESSION['ProfileID'] = $line['ProfileID'];
		if(isset($_POST['remember']) && $_POST['remember'] =='yes')
		{
			$_SESSION['RememberMe'] = 1;
			$_SESSION['UserName'] = $_POST['user'];
			$RememberMe = true;
		}
		else
		{
			$_SESSION['RememberMe'] = 0;
			$_SESSION['UserName'] = $_POST['user'];
			$RememberMe = false;
		}
		$loggingIn = true;
		header("refresh: 2; ProfilePage.php?pid=".$_SESSION['ProfileID']);
	}
	else
	{
		$query = 'UPDATE Profile set FailedPass=FailedPass+1 WHERE UserName="'.$_POST['user'].'"';
		mysql_query($query, $dblink) or die("Retrieve query failed: $query ".mysql_error());
		$error = true;
		$failedLoggIn = true;
		$RememberMe = 0;
	}
}
else
{
		if(isset($_SESSION['RememberMe']) && isset($_SESSION['UserName']))
	{
		$userName = $_SESSION['UserName'];
		$RememberMe = true;
	}
	else
	{
		$userName = '';
		$RememberMe = false;
	}
}
include 'TemplatePage.php';
?>
<title>Login</title>
</head>
<body>
<h1>Login</h1>
<?php
if($loggingIn)
{
	echo 'You have sucessfully logged in Redirecting to Profile Page in 2 seconds';
}
elseif($failedLoggIn)
{
	echo 'password/username combination unsuccessful';
}
?>
<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
<p>
UserName<br />
<input name="user" type="text" value="<?php echo $userName; ?>" /><br />
</p>
<p>
Password<br />
<input name="pass" type="password" /><br />
</p>
<p>
<input type="checkbox" name="remember" value="yes" <?php echo (($RememberMe)?'checked':'') ?> />remember me<br />
</p>
<p>
<input type="submit" name="login" value="Login" />
</p>
<?php
include 'footer.php';
?>