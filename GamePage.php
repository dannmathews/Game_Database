<?php
session_start();
include 'Functions.php';
if(isset($_SESSION['ProfileID']))
{
	$ProfileID = $_SESSION['ProfileID'];
	$UserName = $_SESSION['UserName'];
}
else
{
	$ProfileID = 0;
	$UserName = 'Guest';
}
require_once 'backendfiles/dbconfig.php';
if(isset($_GET['gid']))
{
	$GameID = $_GET['gid'];
}
else
{
	$GameID = 0;
}
if(isset($_POST['cancel']))
{
	header("location:GamePage.php?gid=".$GameID);
}
if(isset($_POST['editdownload']))
{
	$editFile = true;
}
else
{
	$editFile = false;
}
if(isset($_POST['editscreen']))
{
	$editScreen = true;
}
else
{
	$editScreen = false;
}
if(isset($_POST['editdesc']))
{
	$editDesc = true;
}
else
{
	$editDesc = false;
}
if(isset($_POST['editinfo']))
{
	$editInfo = true;
}
else
{
	$editInfo = false;
}

if(isset($_POST['postIt']) && !empty($_POST['comment']))
{
	$comment = mysql_real_escape_string(trim($_POST['comment']));
	if(!empty($_POST['subject']))
	{
		$subject = mysql_real_escape_string(trim($_POST['subject']));
	}
	else
	{
		$subject = 'No Subject';
	}
	if(isset($_POST['reply']))
	{
		$parent = $_POST['reply'];
		$insertComment = 'INSERT INTO Comment(GameID, ProfileID, DateSubmitted, Subject, Comment, ParentPost) VALUES ('.$GameID.', '.$ProfileID.', CURDATE(), "'.$subject.'", "'.$comment.'", '.$parent.')';
		mysql_query($insertComment, $dblink) or die("Add query failed: $insertComment " . mysql_errno() . ": " . mysql_error());
	}
	else
	{
		$insertComment = 'INSERT INTO Comment(GameID, ProfileID, DateSubmitted, Subject, Comment, ParentPost) VALUES ('.$GameID.', '.$ProfileID.', CURDATE(), "'.$subject.'", "'.$comment.'", 0)';
		mysql_query($insertComment, $dblink) or die("Add query failed: $insertComment " . mysql_errno() . ": " . mysql_error());
	}
	header("location:GamePage.php?gid=".$GameID);
}
if(isset($_POST['updatescreen']))
{
	if (isset($_FILES['newscreen']) && is_uploaded_file($_FILES['newscreen']['tmp_name']) && preg_match('/\Aimage\//',$_FILES['newscreen']['type']) && $_FILES['newscreen']['error'] == 0)
	{
		$photo = mysql_real_escape_string(file_get_contents($_FILES['newscreen']['tmp_name']));
		insertScreenShot($GameID, $_FILES['newscreen']['size'], $_FILES['newscreen']['type'], $_FILES['newscreen']['name'], $_FILES['newscreen']['tmp_name'], $dblink);
	}
	if(!empty($_POST['deletescreen']))
	{
		foreach ($_POST['deletescreen'] as $key => $value)
		{
			deleteScreen($value, $GameID, $dblink);
		}
	}
	header("location:GamePage.php?gid=".$GameID);
}
if(isset($_POST['updatedesc']))
{
	updateDesc($_POST['updatedesc'], $GameID, $dblink);
	header("location:GamePage.php?gid=".$GameID);
}
if(isset($_POST['updateinfo']))
{
	updateGenre($GameID, $_POST['genre'], $dblink);
	updatePlatform($GameID, $_POST['platform'], $dblink);
	updatePlayer($GameID, $_POST['minP'], $_POST['maxP'], $dblink);
	updateEngine($GameID, $_POST['engine'], $dblink);
	updateWebsite($GameID, $_POST['website'], $dblink);
	header("location:GamePage.php?gid=".$GameID);
}
if(isset($_POST['updatefile']))
{
	if(!empty($_POST['removefile']))
	{
		foreach($_POST['removefile'] as $key => $value)
		{
			deleteFile($value, $dblink);
		}
	}
	if(isset($_FILES['uploadpid0']) && is_uploaded_file($_FILES['uploadpid0']['tmp_name']) && $_FILES['uploadpid0']['error'] == 0)//Insert game file
		{
			insertUpload($GameID, 0, $_FILES['uploadpid0']['size'], $_FILES['uploadpid0']['type'], $_FILES['uploadpid0']['name'], $_FILES['uploadpid0']['tmp_name'], $dblink);
		}
	$queryPlatform = 'SELECT PlatformID FROM HasPlatform WHERE GameID='.$GameID;
	$resultPlatform = mysql_query($queryPlatform, $dblink) or die("unsuccessful");
	while($linePlatform = mysql_fetch_assoc($resultPlatform))
	{
		if(isset($_FILES['uploadpid'.$linePlatform['PlatformID']]) && is_uploaded_file($_FILES['uploadpid'.$linePlatform['PlatformID']]['tmp_name']) && $_FILES['uploadpid'.$linePlatform['PlatformID']]['error'] == 0)
		{
			insertUpload($GameID, $linePlatform['PlatformID'], $_FILES['uploadpid'.$linePlatform['PlatformID']]['size'], $_FILES['uploadpid'.$linePlatform['PlatformID']]['type'], $_FILES['uploadpid'.$linePlatform['PlatformID']]['name'], $_FILES['uploadpid'.$linePlatform['PlatformID']]['tmp_name'], $dblink);
		}
	}
	header("location:GamePage.php?gid=".$GameID);
}
if(isset($_POST['rate']))
{
	updateRating($GameID, $ProfileID, $_POST['rating'], $dblink);
	header("location:GamePage.php?gid=".$GameID);
}
include 'TemplatePage.php';
//Game Infornation
if($GameID > 0)
{
	$queryGame = 'SELECT a.Name, b.UserName, b.ProfileID, a.GameDescription, a.NumberofRatings, a.CurrentRating, a.DateReleased, a.DateSubmitted, a.MaxPlayers, a.MinPlayers, a.Engine, a.Website FROM Game a INNER JOIN Profile b ON a.ProfileID=b.ProfileID WHERE a.GameID='.$GameID;
	$resultGame = mysql_query($queryGame, $dblink) or die("Add query failed: $queryGame " . mysql_errno() . ": " . mysql_error());
	if($lineGame = mysql_fetch_assoc($resultGame))
	{
		$gameName = $lineGame['Name'];
		$gameSubmitter = $lineGame['UserName'];
		$gameDescription = $lineGame['GameDescription'];
		$gameNumRatings = $lineGame['NumberofRatings'];
		$gameCurrRating = $lineGame['CurrentRating'];
		$gameReleased = $lineGame['DateReleased'];
		$gameSubmitted = $lineGame['DateSubmitted'];
		$gameMaxP = $lineGame['MaxPlayers'];
		$gameMinP = $lineGame['MinPlayers'];
		$gameEngine = $lineGame['Engine'];
		$gameWebsite = $lineGame['Website'];
		$GameProfileID = $lineGame['ProfileID'];
		if(($GameProfileID == $ProfileID) || ($ProfileID == 1))
		{
			$canedit = true;
		}
		else
		{
			$canedit = false;
		}
	}
	else
	{
		echo 'Game Does not exist or has been deleted';
		exit();
	}
}
else
{
	$gameName = 'Game Name';
	$gameDescription = 'Game Description Goes here';
	$gameNumRatings = 0;
	$gameCurrRating = 0;
	$gameReleased = '06-06-2006';
	$gameSubmitted = '06-06-2006';
	$gameMaxP = 1;
	$gameMinP = 1;
	$gameEngine = 'C++';
	$gameWebsite = 'http://www.csumb.edu';
}
?>
<title>
<?php
echo $gameName;
?>
</title>
</head>
<body>
<h1>
<?php
echo $gameName;
?>
</h1>
<div id="screenshots">
<h2>ScreenShots</h2><?php
echo ((!$editScreen && $canedit)?'<form action="'.$_SERVER['PHP_SELF'].'?gid='.$GameID.'#screenshots" method="post"><input type="submit" name="editscreen" value="Edit Screen Shots" /></form>':'').'
<br />';
$queryGamePicture = 'SELECT ScreenShotID FROM ScreenShot WHERE GameID='.$GameID.' ORDER BY FileEntryDate';
$resultGamePicture = mysql_query($queryGamePicture, $dblink) or die("Query Failed");
$imageCount = 0;
while($lineGamePicture = mysql_fetch_assoc($resultGamePicture))
{
	if($imageCount++ == 0)
	{
		echo '<table><tr>';
	}
	echo '<td>';
	echo '<img src="backendfiles/showfile.php?id='.$lineGamePicture['ScreenShotID'].'&amp;type=F&amp;loc=Screen"><img src="showfile.php?id='.$lineGamePicture['ScreenShotID'].'&amp;type=T&amp;loc=Screen"/>';
	echo '</td>';
}
if($imageCount == 0)
{
	echo 'no screenshots for this game';
}
echo '</tr><tr>';
if($editScreen)
{
	echo '<form action="'.$_SERVER['PHP_SELF'].'?gid='.$GameID.'" method="post" enctype="multipart/form-data">';
	for($increment = 0; $increment < $imageCount; $increment++)
	{
		echo '<td><input type="checkbox" value="'.$increment.'" name="deletescreen[]" />remove image</td>';
	}
	echo '<td><input type="file" name="newscreen" />';
	
	echo '</td></tr></table><br /><input type="submit" name="updatescreen" value="Update ScreenShots" /><input type="submit" name="cancel" value="Camcel" /></form>';
}
else
{
	echo '</td></tr></table>';
}
?>
</div>
<table>
<td>

<h2>Game Description</h2>
<?php echo ((!$editDesc && $canedit)?'<form action=" '.$_SERVER['PHP_SELF'].'?gid='.$GameID.'#description" method="post"><input type="submit" name="editdesc" value="Edit Game Description" /></form>':''); ?>
</td>
<td valign="top">
<h2>Game Rating</h2>
</td>
<tr width="300px" valign="top">
<td>
<p style="margin:0 40px 0;">
<div id="description">
<?php
if($editDesc)
{
	echo '<form action="'.$_SERVER['PHP_SELF'].'?gid='.$GameID.'" method="post"><textarea name="newdesc" value="'.$gameDescription.'"></textarea><br /><input type="submit" name="updatedesc">
	<input name="cancel" type="submit" value="Cancel" /></form>';
}
else
{
	echo $gameDescription;
}
?>
</div>
</p>
</td>
<td>
<div id="rating">
<?php
echo 'Rating:'.$gameCurrRating.'<br />
from '.$gameNumRatings.' ratings<br />
your rating:<br />';
$queryRating = 'SELECT Rating FROM HasRatings WHERE GameID='.$GameID.' AND ProfileID='.$ProfileID;
$resultRating = mysql_query($queryRating, $dblink) or die("Can't get rating info");
if($lineRating = mysql_fetch_assoc($resultRating))
{
	echo $lineRating['Rating'];
}
else if(isset($_SESSION['ProfileID']))
{
	echo '<form action="'.$_SERVER['PHP_SELF'].'?gid='.$GameID.'" method="post">
	<select name="rating">
	<option value="0.0"></option>
	<option value="1.0">1:Terrible</option>
	<option value="2.0">2:Poor</option>
	<option value="3.0">3:Fair</option>
	<option value="4.0">4:Good</option>
	<option value="5.0">5:Great</option>
	</select><br />
	<input type="submit" name="rate" value="Rate" />
	</form>';
}
else
{
	echo 'you must login to rate this game';
}
?>
</div>
</td>
</tr>
</table>
<table>
<td>
<h2>Game Info</h2>
<div id="info">
<?php echo ((!$editInfo && $canedit)?'<form action="'. $_SERVER['PHP_SELF'].'?gid='.$GameID.'#info" method="post"><input type="submit" name="editinfo" value="Edit Game Info" /></form>':''); ?>
</td>
<tr>
<td>
<b>Date of Release:</b></td>
<td>
<?php
echo dateSQLToPHP($gameReleased);
?>
</td>
</tr>
<tr><td>
<b>Date Submitted:</b></td>
<td>
<?php
echo dateSQLToPHP($gameSubmitted);
?>
</td>
</tr>
<tr><td>
<b>Submitter:</b></td>
<td>
<?php
echo '<a href="ProfilePage.php?pid='.$GameProfileID.'">'.$gameSubmitter.'</a>';
?>
</td>
</tr>
<tr><td>
<b>Genre(s):</b></td>
<td>
<?php
	$queryHasGenre = 'SELECT a.GenreDesc, a.GenreID FROM Genre a INNER JOIN HasGenre b ON a.GenreID = b.GenreID WHERE b.GameID = '.$GameID.' ORDER BY a.GenreDesc';
	$resultHasGenre = mysql_query($queryHasGenre, $dblink) or die("Add query failed: $queryRating " . mysql_errno() . ": " . mysql_error());
if($editInfo)
{
	$totalGenre = 0;
	while($lineHasGenre = mysql_fetch_assoc($resultHasGenre))
	{
		$GameGenre[$totalGenre++] = $lineHasGenre['GenreID'];
	}
	echo '<form action="'.$_SERVER['PHP_SELF'].'?gid='.$GameID.'" method="post">';
$queryGenre = "SELECT * FROM Genre ORDER BY GenreDesc";
$resultGenre = mysql_query($queryGenre, $dblink) or die("Retrieve query failed: $queryGenre ".
mysql_error());
echo '<table><tr><td>';
$lineCount = mysql_num_rows($resultGenre);
$countGenre = 0;
$count = 0;
while($lineGenre = mysql_fetch_assoc($resultGenre))
{
	if($count++ >= $lineCount/2)
	{
		echo '</td><td>';
		$count = 0;
	}
	$genre = $lineGenre['GenreDesc'];
	echo '<input ';
	if(isset($GameGenre) && $countGenre < $totalGenre && $GameGenre[$countGenre] == $lineGenre['GenreID'])
	{
		echo 'checked="checked" ';
		$countGenre++;
	}
	echo 'type="checkbox" name="genre[]" value="'.$lineGenre['GenreID'].'" /> '.$genre."<br />";
}
echo '</td></tr></table>';

}
else
{
	$next = false;
	while($lineHasGenre = mysql_fetch_assoc($resultHasGenre))
	{
		if($next)
		{
			echo ', ';
		}
		else
		{
			$next = true;
		}
		echo $lineHasGenre['GenreDesc'];
	}
	if(!$next)
	{
		echo 'No Genre\'s listed';
	}
}
?>
</td>
</tr>
<tr><td>
<b>Platform(s):</b></td>
<td>
<?php
if($editInfo)
{
	$queryHasPlatform = 'SELECT PlatformID FROM HasPlatform WHERE GameID='.$GameID.' ORDER BY PlatformID';
	$resultHasPlatform = mysql_query($queryHasPlatform, $dblink) or die("Can't retireve HasPlatforms");
	$totalPlat = 0;
	while($lineHasPlatform = mysql_fetch_assoc($resultHasPlatform))
	{
		$GamePlatform[$totalPlat++] = $lineHasPlatform['PlatformID'];
	}
	$queryPlatform = 'SELECT * FROM Platform ORDER BY PlatformID';
	$resultPlatform = mysql_query($queryPlatform) or die("Retrieve query failed: $queryGenre ". mysql_error());
	$count = 0;
	echo '<table><tr><td>';
	$lineCount = mysql_num_rows($resultPlatform);
	$countPlat = 0;
	$count = 0;
	while($linePlatform = mysql_fetch_assoc($resultPlatform))
	{
		if($count++ >= $lineCount/2)
		{
			echo '</td><td>';
			$count = 0;
		}
		$platform = $linePlatform['PlatformDesc'];
		echo '<input';
		if(isset($GamePlatform) && $countPlat < $totalPlat && $GamePlatform[$countPlat] == $linePlatform['PlatformID'])
		{
			echo ' checked="checked"';
			$countPlat++;
		}
		echo ' type="checkbox" name="platform[]" value="'.$linePlatform['PlatformID'].'" /> '.$platform."<br />";
	}
	echo '</td></table>';
}
else
{
	$queryPlatform = 'SELECT a.PlatformDesc FROM HasPlatform b INNER JOIN Platform a ON a.PlatformID = b.PlatformID WHERE b.GameID = '.$GameID.' ORDER BY a.PlatformDesc';
	$resultPlatform = mysql_query($queryPlatform, $dblink) or die("Add query failed: $queryPlatform " . mysql_errno() . ": " . mysql_error());
	$next = false;
	while($linePlatform = mysql_fetch_assoc($resultPlatform))
	{
		if($next)
		{
			echo ', ';
		}
		else
		{
			$next = true;
		}
		echo $linePlatform['PlatformDesc'];
	}
	if(!$next && !$editInfo)
	{
		echo 'No Platforms listed';
	}
}
?>
</td>
</tr>
<tr><td>
<b>Player(s):</b></td>
<td>
<?php
if($editInfo)
{
	echo '<select name="minP">
<option '.(($gameMinP == 1)?'selected ':'').'value="1">1</option>
<option '.(($gameMinP == 2)?'selected ':'').'value="2">2</option>
<option '.(($gameMinP == 3)?'selected ':'').'value="3">3</option>
<option '.(($gameMinP == 4)?'selected ':'').'value="4">4</option>
<option '.(($gameMinP == 5)?'selected ':'').'value="5">5</option>
<option '.(($gameMinP == 6)?'selected ':'').'value="6">6</option>
<option '.(($gameMinP == 7)?'selected ':'').'value="7">7</option>
<option '.(($gameMinP == 8)?'selected ':'').'value="8">8</option>
</select> 
- <select name="maxP">
<option '.(($gameMaxP == 1)?'selected ':'').'value="1">1</option>
<option '.(($gameMaxP == 2)?'selected ':'').'value="2">2</option>
<option '.(($gameMaxP == 3)?'selected ':'').'value="3">3</option>
<option '.(($gameMaxP == 4)?'selected ':'').'value="4">4</option>
<option '.(($gameMaxP == 5)?'selected ':'').'value="5">5</option>
<option '.(($gameMaxP == 6)?'selected ':'').'value="6">6</option>
<option '.(($gameMaxP == 7)?'selected ':'').'value="7">7</option>
<option '.(($gameMaxP == 8)?'selected ':'').'value="8">8</option>
</select>';
}
else
{
	echo $gameMinP.' - '.$gameMaxP;
}
?>
</td>
</tr>
<tr><td>
<b>Engine:</b></td>
<td>
<?php
if($editInfo)
{
	echo '<input name="engine" type="text" size="60" maxlength="100" value="'.$gameEngine.'" />';
}
else
{
	echo $gameEngine;
}
?>
</td>
</tr>
<tr><td>
<b>Website:</b></td>
<td>
<?php
if($editInfo)
{
	echo '<input name="website" type="text" size="60" maxlength="100" value="'.$gameWebsite.'" />';
}
else
{
	echo $gameWebsite;
}
echo '</td>
</tr>
</table>';
if($editInfo)
{
	echo '<input type="submit" name="updateinfo" value="Update Info" />
	<input type="submit" name="cancel" value="Camcel" /></form>';
}
echo '<h2>Download</h2>';
echo ((!$editFile && $canedit)?'<form action="'. $_SERVER['PHP_SELF'].'?gid='.$GameID.'#download" method="post"><input type="submit" name="editdownload" value="Edit Downloads" /></form>':'') ?>
<div id="download">
<table cellpadding="10">
<tr>
<?php
//Table Header of downloads
$countOfPlatforms = 0;
$queryPlatform = 'SELECT a.PlatformDesc, a.PlatformID FROM Platform a INNER JOIN HasPlatform b ON a.PlatformID = b.PlatformID WHERE b.GameID = '.$GameID.' ORDER BY b.PlatformID';
$resultPlatform = mysql_query($queryPlatform, $dblink) or die("Add query failed: $queryPlatform " . mysql_errno() . ": " . mysql_error());
while($linePlatform = mysql_fetch_assoc($resultPlatform))
{
	$platforms[$countOfPlatforms++] = $linePlatform['PlatformID'];
	echo '<td><b>'.$linePlatform['PlatformDesc'].'</b></td>';
}
?>
<td>
<b>Platform not specified</b>
</td>
</tr>
<tr>
<?php
$count = 0;
if($editFile)
{
	echo '<form action="'.$_SERVER['PHP_SELF'].'?gid='.$GameID.'" enctype="multipart/form-data" method="post">';
}
$queryDownload = 'SELECT UploadID, FileSize, FileName, PlatformID FROM Upload WHERE GameID='.$GameID.' AND PlatformID!=0 ORDER BY PlatformID';
$resultDownload = mysql_query($queryDownload, $dblink) or die("Failed to achieve download: $queryDownload : ".mysql_error());
while($lineDownload = mysql_fetch_assoc($resultDownload))
{
	if(isset($platforms))
	{
		while($platforms[$count++] != $lineDownload['PlatformID'])
		{
			echo '<td>No file available for download<br />'.(($editFile)?'<input type="file" name="uploadpid'.$platforms[$count-1].'" />':'').'</td>';
		}
	}
	echo '<td><a href="download.php?id='.$lineDownload['UploadID'].'">'.$lineDownload['FileName'].'</a><br />Size: '.kbtomb($lineDownload['FileSize']).' mb</td>';
}
while($count++ < $countOfPlatforms)
{
	echo '<td>No file available for download<br />'.(($editFile)?'<input type="file" name="uploadpid'.$platforms[$count-1].'" />':'').'</td>';
}
$queryDownload = 'SELECT UploadID, FileSize, FileName, PlatformID FROM Upload WHERE GameID='.$GameID.' AND PlatformID=0';
$resultDownload = mysql_query($queryDownload, $dblink) or die("Failed to achieve download: $queryDownload : ".mysql_error());
if($lineDownload = mysql_fetch_assoc($resultDownload))
{
	echo '<td><a href="download.php?id='.$lineDownload['UploadID'].'">'.$lineDownload['FileName'].'</a><br />Size: '.kbtomb($lineDownload['FileSize']).' mb'.(($editFile)?'<br /><input name="removefile[]" type="checkbox" value="'.$lineDownload['UploadID'].'" />delete file':'').'</td>';
}
else
{
	echo '<td>No file available for download<br />'.(($editFile)?'<input type="file" name="uploadpid0" />':'').'</td>';
}
echo '</tr>
</table>';
if($editFile)
{
	echo '<input type="submit" name="updatefile" />
	<input name="cancel" type="submit" value="Cancel" /></form>';
}
?>
</div>
<h2>Comments:</h2>
<?php
if(isset($_GET['reply']))
{
	$reply = $_GET['reply'];
}
else
{
	$reply = 0;
}
displayComment($ProfileID, $GameID, 0, 0, $reply, $UserName, $dblink);
/*protype comment<table style="margin-left:20px;">
<td valign="top"><img src="paulpaul.png" /></td>
<td valign="top">Subject goes here<br />
by UserName
<p>
I don't like this game at all.  It is an insult to the gamers of the world and should be banned in all countires.  Heck, it probably should be banned from the universe.  Maybe someday we should take this game along with the remaining copies of E.T. and drop it into a black hole in the universe.  Maybe some low tech society will find it and discover how to modernize to our technology.  Then we will have intelligence equal to us in the universe and not be alone anymore.
</p></td>
<td valign="top"><a href="you.html">Reply</a><br />
Positive: 5<br />
Negative: 10<br />
Total: 15</td>
</table>*/


echo '<a href="GamePage.php?gid='.$GameID.'&reply=0#newcomment">Insert New Comment</a>';
if(isset($_GET['reply']) && $_GET['reply'] == 0)
{
	echo '<div id="newcomment"><table width="500" style="margin-left:0px;">
	<td valign="top"><a href="ProfilePage.php?pid='.$ProfileID.'"><img src="';
	$queryProPicture = 'SELECT ProfilePictureID FROM ProfilePicture WHERE ProfileID='.$ProfileID;
	$resultProPicture = mysql_query($queryProPicture, $dblink) or die("Query Failed");
	if($lineProPicture = mysql_fetch_assoc($resultProPicture))
	{
		echo 'showfile.php?id='.$lineProPicture['ProfilePictureID'].'&amp;type=T&amp;loc=Pro';
	}
	else
	{
		echo 'paulpaul.png';
	}
	echo '" /></a></td>
	<td valign="top">
	<form action=" '.$_SERVER['PHP_SELF'].'?gid='.$GameID.'" name="new" method="post"><input type="text" name="subject" placeholder="Subject" /><br />
	by <a href="ProfilePage.php?pid='.$ProfileID.'">'.$userName.'</a><br />
	<textarea style="width:300px; height:150px; resize:both" name="comment"></textarea><br />
	<input name="postIt" type="submit" value="Post" />
	<input name="cancel" type="submit" value="Cancel" />
	</form></div>';
}
?>
</td>
</table>
</body>
</html>