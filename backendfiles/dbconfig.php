<?php
//This code establishes connection with the database
$dbhost = 'localhost';
$dbuser = '';
$dbpassword = '';
$dbdatabase = '';

$dblink = mysql_connect($dbhost, $dbuser, $dbpassword)
or die("could not connect to database or $dbhost");

mysql_select_db($dbdatabase,$dblink)
    or die("Could not select database $dbdatabase " . mysql_error());
?>