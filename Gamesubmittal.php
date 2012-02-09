<?php
session_start();
require_once 'backendfiles/dbconfig.php';
include 'Functions.php';
$pleaseLogin = false;
if(isset($_SESSION['ProfileID']))
{
	$ProfileID = $_SESSION['ProfileID'];
	$UserName = $_SESSION['UserName'];
}
else
{
	$pleaseLogin = true;
}
include 'TemplatePage.php';
$nameError = false;
$descriptionError = false;
$name = '';
$description = '';
$engine ='';
$website = '';
if (isset($_POST['action'])) {
	$error = FALSE;

	if (!empty($_POST['name']))
	{
		mysql_real_escape_string(trim($name = $_POST['name']));
	}
	else
	{
		$nameError = true;
		$error = true;
	}
	if(!empty($_POST['Description']))
	{
		mysql_real_escape_string(trim($description = $_POST['Description']));
	}
	else
	{
		$descriptionError = true;
		$error = true;
	}
	$minP = $_POST['minP'];
	$maxP = $_POST['maxP'];
	mysql_real_escape_string(trim($engine = $_POST['engine']));
	mysql_real_escape_string(trim($website = $_POST['website']));
	$yearReleased = $_POST['Year'];
	$monthReleased = $_POST['Month'];
	$dayReleased = $_POST['Day'];
	if (!$error)
	{
		echo 'Game Submittal Successful, redirecting in 2 seconds';
		$GameId = insertGame($ProfileID, $name, $description, dateTosql($yearReleased, $monthReleased, $dayReleased), $minP, $maxP, $engine, $website, $dblink);
		
		if (isset($_FILES['screenshots']) && is_uploaded_file($_FILES['screenshots']['tmp_name']) && preg_match('/\Aimage\//',$_FILES['screenshots']['type']) && $_FILES['screenshots']['error'] == 0)
		{
			list($width, $height, $type, $attr) = getimagesize($_FILES['screenshots']['tmp_name']);
			insertScreenshot($GameId, $_FILES['screenshots']['size'], $_FILES['screenshots']['type'], $_FILES['screenshots']['name'], $_FILES['screenshots']['tmp_name'], $dblink);
		}
		if(!empty($_POST['genre']))
		{
			foreach ($_POST['genre'] as $key => $value)
			{
				insertGenre($value, $GameId, $dblink);
			}
		}
		if(!empty($_POST['platform']))
		{
			foreach ($_POST['platform'] as $key => $value)
			{
				insertPlatform($value, $GameId, $dblink);
			}
		}
		if(isset($_FILES['files']) && is_uploaded_file($_FILES['files']['tmp_name']) && $_FILES['files']['error'] == 0)//Insert game file
		{
			echo 'uploading';
			insertUpload($GameId, 0, $_FILES['files']['size'], $_FILES['files']['type'], $_FILES['files']['name'], $_FILES['files']['tmp_name'], $dblink);
		}
		$queryGameID = 'SELECT GameID FROM Game WHERE Name="'.$name.'"';
		$resultGaneID = mysql_query($queryGameID, $dblink) or die("Add query failed: $queryGameID " . mysql_errno() . ": " . mysql_error());
		$lineGameID = mysql_fetch_assoc($resultGaneID);
		header("refresh: 2; GamePage.php?gid=".$lineGameID['GameID']);
		exit();
	}
}
else
{
	$name = '';
	$description = '';
	$yearReleased = date('Y');
	$monthReleased = date('m');
	$dayReleased = date('d');
	$minP = 1;
	$maxP = 1;
	
	$engine = '';
	$website = '';
}
if($pleaseLogin)
{
	echo 'Guests may not submit games, please <a href="Login.php">login</a>';
	exit();
}
?>
<title>Game Submittal Page</title>
</head>
<body>
<h1 align="center">Game submittal form</h1>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
<p>Name: <abbr style="font-size:100%; color:#F00" title="required">*</abbr> <input name="name" type="text" size="60" maxlength="100" />
<br />
Game Description:
<abbr style="font-size:100%; color:#F00" title="required">*</abbr> <br /><textarea name="Description" rows="6" cols="60"></textarea><br />
<br />
<?php
echo '<table>
<td>
Game genre(s): <br />';
$queryGenre = "SELECT * FROM Genre ORDER BY GenreDesc";
$resultGenre = mysql_query($queryGenre, $dblink) or die("Retrieve query failed: $queryGenre ".
mysql_error());
$count = 0;
echo '<table><tr><td>';
$lineCount = mysql_num_rows($resultGenre);
$count = 0;
while($lineGenre = mysql_fetch_assoc($resultGenre))
{
	if($count++ >= $lineCount/2)
	{
		echo '</td><td>';
		$count = 0;
	}
	$genre = $lineGenre['GenreDesc'];
	echo '<input type="checkbox" name="genre[]" value="'.$lineGenre['GenreID'].'" /> '.$genre."<br />";
}
echo '</td></tr></table>';
echo '</td><td valign="top">';
echo 'Game Platform(s):';
$queryPlatform = 'SELECT * FROM Platform ORDER BY PlatformDesc';
$resultPlatform = mysql_query($queryPlatform) or die("Retrieve query failed: $queryGenre ". mysql_error());
$count = 0;
echo '<table><tr><td>';
$lineCount = mysql_num_rows($resultPlatform);
$count = 0;
while($linePlatform = mysql_fetch_assoc($resultPlatform))
{
	if($count++ >= $lineCount/2)
	{
		echo '</td><td>';
		$count = 0;
	}
	$platform = $linePlatform['PlatformDesc'];
	echo '<input type="checkbox" name="platform[]" value="'.$linePlatform['PlatformID'].'" /> '.$platform."<br />";
}
echo '</td></table>';
echo '</td></tr></table>';
?>
<br />
Date Released: Month:<select name="Month">
<?php
$month = date("m");
echo '<option '.(($month=='01')?'selected="selected" ':'').'value="01">January</option>
<option '.(($month=='02')?'selected="selected" ':'').'value="02">Feburary</option>
<option '.(($month=='03')?'selected="selected" ':'').'value="03">March</option>
<option '.(($month=='04')?'selected="selected" ':'').'value="04">April</option>
<option '.(($month=='05')?'selected="selected" ':'').'value="05">May</option>
<option '.(($month=='06')?'selected="selected" ':'').'value="06">June</option>
<option '.(($month=='07')?'selected="selected" ':'').'value="07">July</option>
<option '.(($month=='08')?'selected="selected" ':'').'value="08">August</option>
<option '.(($month=='09')?'selected="selected" ':'').'value="09">September</option>
<option '.(($month=='10')?'selected="selected" ':'').'value="10">October</option>
<option '.(($month=='11')?'selected="selected" ':'').'value="11">November</option>
<option '.(($month=='12')?'selected="selected" ':'').'value="12">December</option>';
?>
</select>
Day: <select name="Day">
<?php
$day = date("d");
echo '<option '.(($day==01)?'selected="selected" ':'').'value="01">01</option>
<option '.(($day==02)?'selected="selected" ':'').'value="02">02</option>
<option '.(($day==03)?'selected="selected" ':'').'value="03">03</option>
<option '.(($day==04)?'selected="selected" ':'').'value="04">04</option>
<option '.(($day==05)?'selected="selected" ':'').'value="05">05</option>
<option '.(($day==06)?'selected="selected" ':'').'value="06">06</option>
<option '.(($day==07)?'selected="selected" ':'').'value="07">07</option>
<option '.(($day==08)?'selected="selected" ':'').'value="08">08</option>
<option '.(($day==09)?'selected="selected" ':'').'value="09">09</option>
<option '.(($day==10)?'selected="selected" ':'').'value="10">10</option>
<option '.(($day==11)?'selected="selected" ':'').'value="11">11</option>
<option '.(($day==12)?'selected="selected" ':'').'value="12">12</option>
<option '.(($day==13)?'selected="selected" ':'').'value="13">13</option>
<option '.(($day==14)?'selected="selected" ':'').'value="14">14</option>
<option '.(($day==15)?'selected="selected" ':'').'value="15">15</option>
<option '.(($day==16)?'selected="selected" ':'').'value="16">16</option>
<option '.(($day==17)?'selected="selected" ':'').'value="17">17</option>
<option '.(($day==18)?'selected="selected" ':'').'value="18">18</option>
<option '.(($day==19)?'selected="selected" ':'').'value="19">19</option>
<option '.(($day==20)?'selected="selected" ':'').'value="20">20</option>
<option '.(($day==21)?'selected="selected" ':'').'value="21">21</option>
<option '.(($day==22)?'selected="selected" ':'').'value="22">22</option>
<option '.(($day==23)?'selected="selected" ':'').'value="23">23</option>
<option '.(($day==24)?'selected="selected" ':'').'value="24">24</option>
<option '.(($day==25)?'selected="selected" ':'').'value="25">25</option>
<option '.(($day==26)?'selected="selected" ':'').'value="26">26</option>
<option '.(($day==27)?'selected="selected" ':'').'value="27">27</option>
<option '.(($day==28)?'selected="selected" ':'').'value="28">28</option>
<option '.(($day==29)?'selected="selected" ':'').'value="29">29</option>
<option '.(($day==30)?'selected="selected" ':'').'value="30">30</option>
<option '.(($day==31)?'selected="selected" ':'').'value="31">31</option>';
?>
</select>
Year:<select name="Year">
<?php
$year = date("Y");
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
$year -= 1;
echo '<option value="'.$year.'">'.$year.'</option>';
?>
</select>
<br />
Number of players: <select name="minP">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
</select>
To: <select name="maxP">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
</select>
<br />
Engine: <input name="engine" type="text" size="60" maxlength="100" />
<br />
Website: <input name="website" type="text" size="60" maxlength="100" />
<br />
<div style="height: auto; width: 500px; overflow: auto; border: 5px solid #eee; margin-bottom: 1.5em;">
<table>
<td>
Screenshots:<br />
<input type="file" name="screenshots" />
</td>
<td>
Files:<br />
<input type="file" name="files" />
</td>
</table>
</div>
<br />
<input type="submit" name="action" value="Submit Form" />

<input type="reset" value="Reset!" />


</form>
<?php
include 'footer.php';
?>