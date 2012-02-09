<?php
session_start();
require_once 'backendfiles/dbconfig.php';
if(isset($_SESSION['ProfileID']))
{
	$AlreadyLoggedIn = true;
	$ProfileID = $_SESSION['ProfileID'];
	$UserName = $_SESSION['UserName'];
}
else
{
	$AlreadyLoggedIn = false;
}
include 'TemplatePage.php';
if($AlreadyLoggedIn)
{
	echo 'You are already logged in';	
}
include 'Functions.php';

//All variables
$name = '';
$password1 = '';
$password2 = '';
$email = '';
//All prossible errors:
$userBlank = false;
$userTaken = false;
$userFromatIncorrect = false;
$passwordBlank = false;
$passFormatIncorrect = false;
$passwordsNotMatch = false;
$emailFormatIncorrect = false;
//check for possible errors
$eror = false;
if(isset($_POST['Submit']))
{
	if(!empty($_POST['user']))
	{
		$name = mysql_real_escape_string(trim($_POST['user']));
	//UserName must start with a letter with a max of 30 characters
		if(!preg_match("/[A-Za-z][a-zA-Z0-9]{2,29}/", $name))
		{
			$userFromatIncorrect = true;
			$eror = true;
		}
		//check if user name is already taken
		elseif(checkTaken($name, $dblink))
		{
			$userTaken = true;
			$eror = true;
		}
	}
	else
	{
		$userBlank = true;
		$eror = true;
	}
	if(!empty($_POST['pass']))
	{
		$password1 = mysql_real_escape_string(trim($_POST['pass']));
		$password2 = mysql_real_escape_string(trim($_POST['cpass']));
		if(!preg_match('/.{6,32}/', $password1))
		{
			$passFormatIncorrect = true;
			$eror = true;
		}
		elseif($password1!=$password2)
		{
			$passwordsNotMatch = true;
			$eror = true;
		}
	}
	else
	{
		$passwordBlank = true;
		$eror = true;
	}
	if(!empty($_POST['email']))
	{
		mysql_real_escape_string(trim($email = $_POST['email']));
		if(!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/', $email))
		{
			$emailFormatIncorrect = true;
			$eror = true;
		}
	}
	if(!$eror)
	{
		$query = 'INSERT INTO Profile (UserName, Password, DateJoined, ';
		if(!empty($_POST['email']))
		{
			$query .= 'Email, ';
		}
		$query .= 'FailedPass, LastLoggedIn) VALUES ("'.$name.'", "'.$password1.'", CURDATE(), ';
		if(!empty($_POST['email']))
		{
			$query .= '"'.$email.'", ';
		}
		$query .= '0, CURDATE())';
		mysql_query($query, $dblink) or die("Add query failed: $query " . mysql_errno() . ": " . mysql_error());
		$query = 'SELECT ProfileID FROM Profile WHERE UserName="'.$name.'"';
		$result = mysql_query($query, $dblink) or die("Retrieve query failed: $queryGenre ".
mysql_error());
		$line = mysql_fetch_assoc($result);
		$_SESSION['ProfileID'] = $line['ProfileID'];
		$_SESSION['UserName'] = $name;
		header("refresh: 2; ProfilePage.php?pid=".$line['ProfileID']);
		echo "New user creation successful, You will be redirect in 2 seconds";
		exit();
	}
}
?>
<title>New Registration Page</title>
</head>
<body>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<h1>New User Regitration</h1>
UserName:<br /> <input name="user" type="text" value="<?php echo $name; ?>" />
<?php
if($userBlank)
{
	echo '<br />UserName is required';
}
elseif($userFromatIncorrect)
{
	echo '<br />UserName must start with a character and be between 2 and 29 characters';
}
elseif($userTaken)
{
	echo '<br />This UserName is already taken';
}
?>
<br />
Password:<br /> <input name="pass" type="password" />
<?php
if($passFormatIncorrect)
{
	echo '<br />password must be between 6 and 32 characters';
}
elseif($passwordBlank)
{
	echo '<br />passwords is required';
}
elseif($passwordsNotMatch)
{
	echo '<br />passwords do not match';
}
?>
<br />
Confirm Password:<br /> <input name="cpass" type="password" />
<?php
if($passwordsNotMatch)
{
	echo '<br />passwords do not match';
}
?>
<br />
Email:<br />
<input name="email" type="text" value="<?php echo $email; ?>" /><br />
<?php
if($emailFormatIncorrect)
{
	echo 'email format is incorrect';
}
?>
<input type="submit" name="Submit" value="Submit" />
<input type="reset" value="Reset" />
</form>
<?php
include 'footer.php';
?>