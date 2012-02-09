<?php
include_once 'backendfiles/dbconfig.php';
	define("PER_PAGE", 5);
	$startrec =  (isset($_GET['start']) ? intval($_GET['start']) : 0);//current page of search
	$search = $_GET['search'];//Searching for
	$query = 'SELECT SQL_CALC_FOUND_ROWS a.GameID, a.Name, a.ProfileID, a.GameDescription, a.MinPlayers, a.MaxPlayers FROM Game a WHERE a.Name LIKE "%'.$search.'%" ORDER BY Name LIMIT '.$startrec.', '.PER_PAGE;
	$getall = mysql_query($query, $dblink) or die ("Main query failed");
	
	$fresult = mysql_query("SELECT FOUND_ROWS() AS numrows",$dblink) or die("Found_rows() failed " . mysql_error());
$fline = mysql_fetch_assoc($fresult);
$foundrows = $fline['numrows'];
$last = ($startrec + mysql_num_rows($getall));
//Filtering

//Start unimplmented filtering
/*$queryPlatform = "SELECT * FROM Platform ORDER BY PlatformDesc";
$resultPlatform = mysql_query($queryPlatform, $dblink) or die("Retrieve query failed: $queryPlatform ".
mysql_error());

$queryEngine = "SELECT Engine FROM Game ORDER BY Engine";
$resultEngine = mysql_query($queryEngine, $dblink) or die("Retrieve query failed: $queryEngine ".
mysql_error());
echo '<form action="'.$_SERVER['PHP_SELF'].'?search='.$search.'" name="search" method="post">';
echo '<table align="center">
<tr><td>';
echo '<label for="platformsearch">Platform:</label>
<select name="platform">
<option value="default" id="platformsearch"></option>';
while($linePlatform = mysql_fetch_assoc($resultPlatform))
{
	$platform = $linePlatform['PlatformDesc'];
	echo '<option value="'.$platform.'"'.(($platformFilter != null && $platformFilter == $platform)?' selected':'').' value="'.$platform.'">'.$platform.'</option>';
}
echo '</select>
</td><tr><td>
<label for="rating">Minimum Rating:</label>
<select name="minRating" id="rating">
<option value="default"></option>
<option'.(($minRatingFilter != null && $minRatingFilter == 1)?' selected':'').' value="1">1</option>
<option'.(($minRatingFilter != null && $minRatingFilter == 2)?' selected':'').' value="2">2</option>
<option'.(($minRatingFilter != null && $minRatingFilter == 3)?' selected':'').' value="3">3</option>
<option'.(($minRatingFilter != null && $minRatingFilter == 4)?' selected':'').' value="4">4</option>
<option'.(($minRatingFilter != null && $minRatingFilter == 5)?' selected':'').' value="5">5</option>
</select>
</td></tr></tr>
<tr><td>
Players:<select name="minPlayers">
<option value="deafult"></option>
<option'.(($minPlayersFilter != null && $minPlayersFilter == 1)?' selected':'').' value="1">1</option>
<option'.(($minPlayersFilter != null && $minPlayersFilter == 2)?' selected':'').' value="2">2</option>
<option'.(($minPlayersFilter != null && $minPlayersFilter == 3)?' selected':'').' value="3">3</option>
<option'.(($minPlayersFilter != null && $minPlayersFilter == 4)?' selected':'').' value="4">4</option>
<option'.(($minPlayersFilter != null && $minPlayersFilter == 5)?' selected':'').' value="5">5</option>
<option'.(($minPlayersFilter != null && $minPlayersFilter == 6)?' selected':'').' value="6">6</option>
<option'.(($minPlayersFilter != null && $minPlayersFilter == 7)?' selected':'').' value="7">7</option>
<option'.(($minPlayersFilter != null && $minPlayersFilter == 8)?' selected':'').' value="8">8</option>
</select>
 to <select name="maxPlayers">
<option value="deafult"></option>
<option'.(($maxPlayersFilter != null && $maxPlayersFilter == 1)?' selected':'').' value="1">1</option>
<option '.(($maxPlayersFilter != null && $maxPlayersFilter == 2)?' selected':'').' value="2">2</option>
<option'.(($maxPlayersFilter != null && $maxPlayersFilter == 3)?' selected':'').' value="3">3</option>
<option'.(($maxPlayersFilter != null && $maxPlayersFilter == 4)?' selected':'').' value="4">4</option>
<option'.(($maxPlayersFilter != null && $maxPlayersFilter == 5)?' selected':'').' value="5">5</option>
<option'.(($maxPlayersFilter != null && $maxPlayersFilter == 6)?' selected':'').' value="6">6</option>
<option'.(($maxPlayersFilter != null && $maxPlayersFilter == 7)?' selected':'').' value="7">7</option>
<option'.(($maxPlayersFilter != null && $maxPlayersFilter == 8)?' selected':'').' value="8">8</option></select>
</td></tr><tr><td>
Engine:<select name="engine">
<option value="default"></option>';
while($lineEngine = mysql_fetch_assoc($resultEngine))
{
	$engine = $lineEngine['Engine'];
	if($engine == 'Not Listed')
	{
		continue;
	}
	echo '<option'.(($engineFilter != null && $engineFilter == $engine)?' selected':'').' value="'.$engine.'">'.$engine.'</option>';
}
echo '</select>
</td></tr>
<tr><td>
<button type="submit" name="search" value="Submit">Search</button>
<button type="reset" value="Reset">Result</button>
</td></tr></table>
</form>';*/
//End unimplemented filtering

//Start Table for browsing search results page
echo '<table align="center" cellpadding="3"><tr><td>' .
	( ($startrec >= PER_PAGE) ?
    ('<a style="text-decoration: none;" onclick="return LoadPage(\'SearchResultList.php?start=' . ($startrec - PER_PAGE) . 
	'&amp;search=' . $search.'\');" 
	href="Search.php?start=' . ($startrec - PER_PAGE) . 
	'&amp;search=' . $search.'">&lt;&lt;&nbsp;PREV</a>') : '&nbsp;') .
    '</td><td align="center" colspan="3">' . $foundrows . ' record' .
    (($foundrows != 1) ? 's' : '') . ' found. Displaying ' .
	($startrec+1) . (($startrec+1) != $last ? (' - ' . $last) : '') . '.</td><td align="right">' .
       ( ( ($startrec + PER_PAGE) < $foundrows) ? 
    ('<a style="text-decoration: none;" onclick="return LoadPage(\'SearchResultList.php?start=' . ($startrec + PER_PAGE) . 
	'&amp;search=' . $search.'\');" 
	href="Search.php?start=' . ($startrec + PER_PAGE) . 
	'&amp;search=' . $search.'">NEXT&nbsp;&gt;&gt;</a>') : '&nbsp;') .
    '</td></tr><tr><td colspan="5">&nbsp;</td></tr></table>';
//End Table for browsing search results page

//Start Display Results
	while($line = mysql_fetch_assoc($getall))
	{
		$GameID = $line['GameID'];
		echo '<table width="800" align="center"><tr><td>';
		//Place picure here
		$queryPicture = 'SELECT ScreenShotID FROM ScreenShot WHERE GameID='.$GameID.' LIMIT 1';
		$resultPicture = mysql_query($queryPicture, $dblink) or die("Query Failed");
		if($linePicture = mysql_fetch_assoc($resultPicture))
		{
			echo '<a href="GamePage.php?gid='.$GameID.'"><img src="backendfiles/showfile.php?id='.$linePicture['ScreenShotID'].'&amp;type=T&amp;loc=Screen" /></a>';
		}
		else
		{
			echo '<a href="GamePage.php?gid='.$GameID.'"><img src="paulpaul.png" /></a>';
		}
		echo '</td><td>';
	echo '<table><tr><td>
	</td>
	<td>
	<a href="GamePage.php?gid='.$GameID.'">'.$line['Name'].'</a><br />
	<b>Players: </b>'.$line['MinPlayers'].'-'.$line['MaxPlayers'].'<br />
	<b>Genre(s): </b>';
	$queryHasGenre = 'SELECT a.GenreDesc FROM Genre a INNER JOIN HasGenre b ON a.GenreID = b.GenreID WHERE b.GameID= '.$GameID;
	$resultHasGenre = mysql_query($queryHasGenre, $dblink) or die("Add query failed: $queryHasGenre " . mysql_errno() . ": " . mysql_error());
	$next = false;
	$count = 0;
	while($lineHasGenre = mysql_fetch_assoc($resultHasGenre))
	{
		$count++;
		if($next)
		{
			echo ', ';
		}
		$next = true;
		echo $lineHasGenre['GenreDesc'];
	}
	if($count == 0)
	{
		echo 'No Genre associated with this game';
	}
	echo '<br /><b>Platform(s): </b>';
	$queryPlatform = 'SELECT a.PlatformDesc FROM Platform a INNER JOIN HasPlatform b ON a.PlatformID = b.PlatformID WHERE b.GameID = '.$GameID;
	$resultPlatform = mysql_query($queryPlatform, $dblink) or die("Add query failed: $queryPlatform " . mysql_errno() . ": " . mysql_error());
	$next = false;
	$count = 0;
	while($linePlatform = mysql_fetch_assoc($resultPlatform))
	{
		$count++;
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
	if($count == 0)
	{
		echo 'No Platforms associated with this game';
	}
	echo '<br /><b>Description: </b>'.$line['GameDescription'];
	echo '</td></tr></table>';
	echo '</td></tr></table>';
	}
//End Display results
?>