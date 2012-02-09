<?php
//This script logs the user out of the website
session_start();
if(isset($_SESSION['UserName']) && $_SESSION['RememberMe'])
{
	$UserName = $_SESSION['UserName'];
	$RememberMe = true;
}
else
{
	$RememberMe = false;
}
session_destroy();
session_start();
if($RememberMe)//if the user selected remember, remember thier username
{
	$_SESSION['UserName'] = $UserName;
	$_SESSION['RememberMe'] = $RememberMe;
}
header("location:../Login.php");//redirect to login page
?>