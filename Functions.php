<?php
define("MAX_SCREENSHOTS",5);
function updateGenre($GameID, $genre, $dblink)
{
	if(!empty($genre))
	{
		$queryDelete = 'DELETE FROM HasGenre WHERE GameID='.$GameID;
		foreach($genre as $key => $value)
		{
			$queryInsert = 'INSERT IGNORE INTO HasGenre (GenreID, GameID)
			VALUES ('.$value.', '.$GameID.')';
			mysql_query($queryInsert, $dblink) or
			die("Genre Insert Failed: $queryInsert : ".mysql_error());
			$queryDelete .= ' AND GenreID!='.$value;
		}
	}
	else
	{
		$queryDelete = 'DELETE FROM HasGenre WHERE GameID='.$GameID;
	}
	mysql_query($queryDelete, $dblink) or
	die("can't delete excess genres : $queryDelete : ".mysql_error());
}

function updatePlatform($GameID, $platform, $dblink)
{
	if(!empty($platform))
	{
		$queryDeletePlatform = 'DELETE FROM HasPlatform WHERE GameID='.$GameID;
		$queryDeleteUpload = 'DELETE FROM Upload WHERE GameID='.$GameID.' AND PlatformID!=0';
		foreach($platform as $key => $value)
		{
			$queryDeletePlatform .= ' AND PlatformID!='.$value;
			$queryDeleteUpload .= ' AND PlatformID!='.$value;
			$queryInsert = 'INSERT IGNORE INTO HasPlatform (GameID, PlatformID) VALUES
			('.$GameID.', '.$value.')';
			mysql_query($queryInsert, $dblink) or die("Don't mess with me");
		}
	}
	else
	{
		$queryDeletePlatform = 'DELETE FROM HasPlatform WHERE GameID='.$GameID;
		$queryDeleteUpload = 'DELETE FROM Upload WHERE GameID='.$GameID.' AND PlatformID!=0';
	}
	mysql_query($queryDeletePlatform, $dblink) or die("Don't mess with me");
	mysql_query($queryDeleteUpload, $dblink) or die("Don't mess with me");
}

function updatePlayer($GameID, $minP, $maxP, $dblink)
{
	$queryUpdate = 'UPDATE Game SET MinPlayers='.$minP.', MaxPlayers='.$maxP.' WHERE GameID='.$GameID;
	mysql_query($queryUpdate, $dblink) or die("death by user");
}

function updateEngine($GameID, $engine, $dblink)
{
	if($engine == '')
		return;
	$queryCheck = 'SELECT Engine FROM Game WHERE GameID='.$GameID;
	$resultCheck = mysql_query($queryCheck, $dblink) or die("unsuccessful");
	$line = mysql_fetch_assoc($resultCheck);
	if($line['Engine'] != $engine)
	{
		$updateEngine = 'UPDATE Game SET Engine="'.mysql_real_escape_string($engine).'" WHERE GameID='.$GameID;
		mysql_query($updateEngine, $dblink) or die("I don't want to update this :".mysql_error());
	}
}

function updateWebsite($GameID, $website, $dblink)
{
	if($website == '')
		return;
	$queryCheck = 'SELECT Website FROM Game WHERE GameID='.$GameID;
	$resultCheck = mysql_query($queryCheck, $dblink) or die("unsuccessful");
	$line = mysql_fetch_assoc($resultCheck);
	if($line['Website'] != $website)
	{
		$updateUpdate = 'UPDATE Game SET Website="'.mysql_real_escape_string($website).'" WHERE GameID='.$GameID;
		mysql_query($updateWebsite, $dblink) or die("I don't want to update this :".mysql_error());
	}	
}

function updateRating($GameID, $ProfileID, $rating, $dblink)
{
	if($rating == 0)
		return;
	$gameUpdate = 'UPDATE Game SET ';
	//$result = mysql_query("SELECT ProfileID FROM HasRatings WHERE ProfileID=".$ProfileID." AND GameID=".$GameID, $dblink);
	//if(mysql_fetch_assoc($result))
	//{
		//$gameUpdate .= 'CurrentRating='.calculateAverage($GameID, $rating, $dblink).', NumberOfRatings=NumberOfRatings+1
		//WHERE GameID='.$GameID;
		//$insertRating = 'UPDATE HasRatings SET Rating='.$rating.' WHERE GameID='.$GameID.' AND ProfileID='.$ProfileID;
	//}
	//else
	//{
		$gameUpdate .= 'CurrentRating='.calculateAverage($GameID, $rating, $dblink).', NumberOfRatings=NumberOfRatings+1
		WHERE GameID='.$GameID;
		$insertRating = 'INSERT INTO HasRatings (ProfileID, GameID, Rating) VALUES('.$ProfileID.', '.$GameID.', '.$rating.')';
	//}
	mysql_query($gameUpdate, $dblink) or die("Can't update Value :$gameUpdate".mysql_error());
	mysql_query($insertRating, $dblink) or die("Can't insert Rating : $insertRating :".mysql_error());
}

function calculateAverage($GameID, $rating, $dblink)
{
	$query = 'SELECT NumberOfRatings, CurrentRating FROM Game WHERE GameID='.$GameID;
	$result = mysql_query($query, $dblink) or die("not found");
	if($line = mysql_fetch_assoc($result))
	{
		$num = $line['NumberOfRatings'];
		$curr = $line['CurrentRating'];
		$result = (($num*$curr)+$rating)/($num+1);
		return $result;
	}
}

function insertUpload($GameId, $platformID, $size, $type, $name, $data, $dblink)
{
	$query = "INSERT INTO Upload (GameID, PlatformID, FileName,FileSize,FileType,FileEntryDate,FileData)
	   VALUES (".$GameId.", ".$platformID.", '" . mysql_real_escape_string($name) . "', " . intval($size) . ",'" .mysql_real_escape_string($type) . "',Now(),'" .mysql_real_escape_string(file_get_contents($data)) . "')";
	mysql_query($query, $dblink) or die("Insert Query Failed: " .mysql_error());
	echo 'rows affected: '.mysql_affected_rows($dblink);
}

function deleteFile($value, $dblink)
{
	$query = 'DELETE FROM Upload WHERE UploadID='.$value;
	mysql_query($query, $dblink) or die("delete failed");
}

function deleteScreen($increment, $GameID, $dblink)
{
	$query = 'SELECT ScreenShotID FROM ScreenShot WHERE GameID='.$GameID.' ORDER BY FileEntryDate';
	$result = mysql_query($query, $dblink) or die("Query Failed");
	$imageCount = 0;
	while($line = mysql_fetch_assoc($result))
	{
		if($imageCount++ == $increment)
		{
			$delete = 'DELETE FROM ScreenShot WHERE ScreenShotID='.$line['ScreenShotID'];
			mysql_query($delete, $dblink) or die("Delete Failed");
			return;
		}
	}
	return;
}

function insertScreenShot($gameID, $size, $type, $fileName, $profilePicture, $dblink)
{
$queryCheck = 'SELECT ScreenShotID FROM ScreenShot WHERE GameID='.$gameID;
if(mysql_num_rows(mysql_query($queryCheck, $dblink)) >= MAX_SCREENSHOTS)
	return;
$sourcefile = imagecreatefromstring(file_get_contents($profilePicture));
/********* PHOTO PROCESSING ***********/
// Constrain to 600x600
if ( (imagesx($sourcefile) < 600) && (imagesy($sourcefile) < 600) ) {
	$photofile = $sourcefile;
}
else {	// we need to scale down the big image
  if (imagesx($sourcefile) > imagesy($sourcefile)) {
        // landscape orientation
    $newx = 600;
    $newy = round(600/imagesx($sourcefile)*   imagesy($sourcefile));
  }
  else {
        // portrait orientation
    $newx = round(600/imagesy($sourcefile)* imagesx($sourcefile));
    $newy = 600;
  }
  $photofile = imagecreatetruecolor($newx,$newy);
  imagecopyresampled ($photofile, $sourcefile, 0,0, 0,0, $newx, $newy, 
	imagesx($sourcefile), imagesy($sourcefile)); 
}
ob_start();
imagejpeg($photofile);
$photodata = ob_get_clean();
/********* THUMBNAIL PROCESSING ***********/
// Constrain to 150x150
if (imagesx($sourcefile) > imagesy($sourcefile)) {
        // landscape orientation
  $newx = 150;
  $newy = round(150/imagesx($sourcefile)*   imagesy($sourcefile));
}
else {
        // portrait orientation
  $newx = round(150/imagesy($sourcefile)* imagesx($sourcefile));
  $newy = 150;
}
$thumb = imagecreatetruecolor($newx,$newy);
imagecopyresampled ($thumb, $sourcefile, 0,0, 0,0, $newx, $newy, 
	imagesx($sourcefile), imagesy($sourcefile)); 
ob_start();
imagejpeg($thumb);
$thumbdata = ob_get_clean();
$query = "INSERT INTO ScreenShot (GameID, FileName, FileSize, FileType, FileEntryDate, FileData,
		ThumbWidth, ThumbHeight, ThumbData)
	   VALUES (".$gameID.", '" . mysql_real_escape_string($fileName) . "',
	   " . intval($size) . ",'" .
	   mysql_real_escape_string($type) . "',Now(),'" .
	   mysql_real_escape_string($photodata) . "'," .
	   $newx . "," . $newy . ",'" .
	   mysql_real_escape_string($thumbdata) . "')";
	   mysql_query($query) or die("Query failed: $query " . mysql_error());	   
}

function kbtomb($value)
{
	return number_format($value/1048576, 1);
	
	
}

function dateSQLToPHP($date)
{
  if($date == null)
    return 'no date listed';
  $year = strtok($date, '-');
  $month = strtok('-');
  $day = strtok('-');
  return $month.'/'.$day.'/'.$year;
}

function displayComment($CurrProfileID, $GameID, $parentPost, $level, $replyTo, $name, $dblink)
{
	$queryComment = 'SELECT a.CommentID, a.DateSubmitted, a.Subject, a.Comment, a.PositiveFeedback, a.NegativeFeedback, b.UserName, b.ProfileID FROM Comment a INNER JOIN Profile b ON a.ProfileID = b.ProfileID WHERE a.GameID = '.$GameID.' AND a.ParentPost='.$parentPost.' ORDER BY a.DateSubmitted';
	$resultComment = mysql_query($queryComment, $dblink) or die("Add query failed: $queryComment " . mysql_errno() . ": " . mysql_error());
	while($lineComment = mysql_fetch_assoc($resultComment))
	{
		$UserName = $lineComment['UserName'];
		$CommentProfileID = $lineComment['ProfileID'];
		$queryPicture = 'SELECT ProfilePictureID FROM ProfilePicture WHERE ProfileID='.$CommentProfileID;
		$resultPicture = mysql_query($queryPicture, $dblink) or die("Add query failed: $queryPicture " . mysql_errno() . ": " . mysql_error());
		if($linePicture = mysql_fetch_assoc($resultPicture))
		{
			$profilePictureID = $linePicture['ProfilePictureID'];
		}
		else
		{
			$profilePictureID = 0;
		}
		$commentID = $lineComment['CommentID'];
		$dateSubmitted = $lineComment['DateSubmitted'];
		$subject = $lineComment['Subject'];
		$comment = $lineComment['Comment'];
		$positiveFeedback = $lineComment['PositiveFeedback'];
		$negativeFeedback = $lineComment['NegativeFeedback'];
		echo '<table width="500" style="margin-left:'.($level*20).'px;"><td valign="top"><a href="ProfilePage.php?pid='.$CommentProfileID.'"><img src="';
		if($profilePictureID != 0)
		{
			echo'backendfiles/showfile.php?id='.$profilePictureID.'&amp;type=T&amp;loc=Pro" /></td>
		<td valign="top">'.$subject.'<br />
		by <a href="ProfilePage.php?pid='.$CommentProfileID.'">'.$UserName.'</a>
		<p>';
		}
		else
		{
			echo'paulpaul.png" /></a></td>
		<td valign="top">'.$subject.'<br />
		by <a href="ProfilePage.php?pid='.$CommentProfileID.'">'.$UserName.'</a>
		<p>';
		}
		echo $comment;
		echo '</p></td>
		<td valign="top"><a href="GamePage.php?gid='.$GameID.'&reply='.$commentID.'#reply'.$commentID.'">Reply</a>';
		//echo '<br />Positive: '.$positiveFeedback.'<br />Negative: '.$negativeFeedback.'<br />Total: '.($positiveFeedback+$negativeFeedback).';
		echo '</td>
		</table>';
		displayComment($CurrProfileID, $GameID, $commentID, $level+1, $replyTo, $name, $dblink);
		if($replyTo == $commentID)
		{
			echo '<div id="reply'.$replyTo.'"><table width="500" style="margin-left:'.(($level+1)*20).'px; width:300px; height:150px; resize:both">
			<td valign="top"><a href="ProfilePage.php?pid='.$CurrProfileID.'"><img src="';
			$queryProPicture = 'SELECT ProfilePictureID FROM ProfilePicture WHERE ProfileID='.$CurrProfileID;
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
			<form action="'.$_SERVER['PHP_SELF'].'?gid='.$GameID.'" method="post"><input type="text" name="subject" placeholder="Subject" value="RE: '.$subject.'" /><br />
			by <a href="ProfilePage.php?pid='.$CurrProfileID.'">'.$name.'</a><br />
			<textarea style="width:300px; height:150px; resize:both;" name="comment"></textarea><br />
			<input name="postIt" type="submit" value="Post" />
			<input name="cancel" type="submit" value="Cancel" />
			<input name="reply" type="hidden" value="'.$commentID.'" />
			</form>
			</td>
			</table></div>';
		}
	}
}

function insertGenre($GenreID, $GameId, $dblink)
{
	$query = 'INSERT INTO HasGenre (GenreID, GameID) VALUES ('.$GenreID.', '.$GameId.')';
	mysql_query($query, $dblink) or die("query Insert Failed: $query");
}

function insertPlatform($PlatformID, $GameId, $dblink)
{
	$query = 'INSERT INTO HasPlatform (PlatformID, GameID) VALUES ('.$PlatformID.', '.$GameId.')';
	mysql_query($query, $dblink) or die("query Insert Failed: $query");
}

function dateTosql($yearReleased, $monthReleased, $dayReleased)
{
	return ($yearReleased.'-'.$monthReleased.'-'.$dayReleased);
}

function insertGame($profileId, $name, $description, $dateReleased, $minP, $maxP, $engine, $website, $dblink)
{
	$query = 'INSERT INTO Game(ProfileID, Name';
	if($description != null)
	{
		$query .= ', GameDescription';
	}
	$query .= ', DateSubmitted, DateReleased, MinPlayers, MaxPlayers';
	if($engine != null)
	{
		$query .= ', Engine';
	}
	if($website != null)
	{
		$query .= ', Website';
	}
	$query .=') VALUES ('.$profileId.', "'.$name.'"';
	if($description != null)
	{
		$query .= ', "'.$description.'"';
	}
	$query .= ', CURDATE(), "'.$dateReleased.'", '.$minP.', '.$maxP;
	if($engine != null)
	{
		$query .= ', "'.$engine.'"';
	}
	if($website != null)
	{
		$query .= ', "'.$website.'"';
	}
	$query .= ')';
	mysql_query($query, $dblink) or die("Add query failed: $query " . mysql_errno() . ": " . mysql_error());
	$query = 'SELECT GameID FROM Game WHERE Name="'.$name.'"';
	$result = mysql_query($query, $dblink) or die("Retrieve query failed: $queryGenre ".
mysql_error());
	$line = mysql_fetch_assoc($result);
	return $line['GameID'];
}
?>