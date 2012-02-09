<?php
//This script gets images from the database and displays them on the screen
if (empty($_GET['id'])) {
	die("No id specified.");
}

$type = (!empty($_GET['type']) ? $_GET['type'] : 'F');
$loc = (!empty($_GET['loc']) ? $_GET['loc'] : 'Pro');
require_once 'dbconfig.php';

$query = "SELECT FileName,FileSize,FileType,FileEntryDate," .
	(($type != 'T') ? 'FileData' : 'ThumbData AS FileData'); 
	
switch($loc)
{
	case'Pro':
	$query .= " FROM ProfilePicture WHERE ProfilePictureID=" . intval($_GET['id']);
	break;
	case'Screen':
	$query .= " FROM ScreenShot WHERE ScreenShotID=" . intval($_GET['id']);
	break;
	default:
	die("unkown location");
}
$result = mysql_query($query)
or die("Query failed: $query " . mysql_error());
$line = mysql_fetch_assoc($result)
or die("Retrieve failed: id " . $_GET['id'] . " not found. Query: $query");

header('Pragma: cache');
header('Cache-Control: cache');
header('Content-Type: ' . $line['FileType']);
header('Content-Disposition: inline; filename=' . $line['FileName'] . 
'; creation-date="' . gmdate("D, d M Y H:i:s", strtotime($line['FileEntryDate'])) . ' GMT"');

echo $line['FileData'];
