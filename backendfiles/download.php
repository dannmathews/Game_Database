<?php
//This script allows users download code from the website
if (empty($_GET['id'])) {
	die("No id specified.");
}
require_once 'dbconfig.php';

$query = 'SELECT FileName,FileSize,FileType,FileEntryDate, FileData FROM Upload WHERE UploadID='.intval($_GET['id']);
$result = mysql_query($query, $dblink) or die("Query Failed: $query, ".mysql_error());
$line = mysql_fetch_assoc($result) or die("File could not be found");
header('Pragma: cache');
header('Cache-Control: cache');
header('Content-Type: ' . $line['FileType']);
header('Content-Disposition: attachment; filename=' . $line['FileName'] . '; creation-date="' . gmdate("D, d M Y H:i:s", $line['FileEntryDate']) . ' GMT"');
echo $line['FileData'];
?>