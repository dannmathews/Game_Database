<?php
session_start();
include 'Functions.php';
require_once 'backendfiles/dbconfig.php';
if(isset($_SESSION['ProfileID']))
{
	$ProfileID = $_SESSION['ProfileID'];
	$UserName = $_SESSION['UserName'];
}
else
{
	$ProfileID = 0;
	$UserName = 'guest';
}
if(isset($_GET['pid']))
{
	$ThisProfileID = $_GET['pid'];
	$query = 'SELECT UserName, DateJoined, Email, Description, LastLoggedIn FROM Profile WHERE ProfileID="'.$ThisProfileID.'"';
		$result = mysql_query($query, $dblink) or die("Retrieve query failed: $queryGenre ".
mysql_error());
}
else
{
	echo 'no userProfileSpecified';
	exit();
}
if(isset($_POST['cancel']))
{
	header("location:ProfilePage.php?pid=".$ThisProfileID);
}
$lineProfile = mysql_fetch_assoc($result);
if($lineProfile)
{
	$ThisUserName = $lineProfile['UserName'];
	$ThisDateJoined = dateSQLToPHP($lineProfile['DateJoined']);
	$ThisEmail = $lineProfile['Email'];
	$ThisDescription = $lineProfile['Description'];
	$ThisLastLoggedIn = dateSQLToPHP($lineProfile['LastLoggedIn']);
}
else
{
	echo 'No user associated with this Profile';
	exit();
}
if(isset($_POST['edit']))
{
	$edit = true;
}
else
{
	$edit = false;
}
if(isset($_FILES['profilepicture']) && !isset($_POST['delete']))
{
	if (isset($_FILES['profilepicture']) && is_uploaded_file($_FILES['profilepicture']['tmp_name']) && preg_match('/\Aimage\//',$_FILES['profilepicture']['type']) && $_FILES['profilepicture']['error'] == 0)
		{
			$photo = mysql_real_escape_string(file_get_contents($_FILES['profilepicture']['tmp_name']));
			insertProfilePicture($ThisProfileID, $_FILES['profilepicture']['size'], $_FILES['profilepicture']['type'], $_FILES['profilepicture']['name'], $_FILES['profilepicture']['tmp_name'], $dblink);
}
}
else if(isset($_POST['delete']))
{
	$queryDelete = 'DELETE FROM ProfilePicture WHERE ProfileID='.$ThisProfileID;
	mysql_query($queryDelete, $dblink) or die("Add query failed: $queryDelete " . mysql_errno() . ": " . mysql_error());
	header("location:ProfilePage.php?pid=".$ThisProfileID);
}
if(isset($_POST['update']))
{
	$descriptionUpdate = mysql_real_escape_string(trim($_POST['description']));
	$emailUpdate = mysql_real_escape_string(trim($_POST['email']));
		$queryUpdate = 'UPDATE Profile SET ';
		if(preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/', $emailUpdate) || empty($emailUpdate))
		{
			$queryUpdate .= 'Email="'.$emailUpdate.'", ';
		}
	$queryUpdate .= 'Description="'.$descriptionUpdate.'" WHERE ProfileID='.$ProfileID;
	mysql_query($queryUpdate, $dblink) or die("Add query failed: $queryUpdate " . mysql_errno() . ": " . mysql_error());
	header("location:ProfilePage.php?pid=".$ThisProfileID);
}
include 'TemplatePage.php';
?>
  
  	<title><?php echo $ThisUserName; ?></title>
    </head>
<body>
    <table style="margin: 0 40px 0 0">
    	<tr>
    		<td width="100%">
            <?php
			if(!(isset($_POST['edit'])))
			{
				if($ProfileID == 1 || $ThisProfileID == $ProfileID)
				{
            		echo '<form action="'.$_SERVER['PHP_SELF'].'?pid='.$ThisProfileID.'#profileedit" method="post"><input type="submit" name="edit" value="Edit Profile" /></form>';
				}
			}
			?>
    			<h1><?php echo $ThisUserName; ?></h1>
                <br />
                <?php
				$queryProfilePicture = 'SELECT ProfilePictureID, FileType, FileData FROM ProfilePicture WHERE ProfileID='.$ThisProfileID;
				$resultProfilePicture = mysql_query($queryProfilePicture, $dblink) or die("Add query failed: $queryProfilePicture " . mysql_errno() . ": " . mysql_error());
				if($edit)
				{
					echo '<form action="'.$_SERVER['PHP_SELF'].'?pid='.$ThisProfileID.'" method="post" enctype="multipart/form-data">';
				}
				echo '<div id="profileedit">';
				if($lineProfilePicture = mysql_fetch_assoc($resultProfilePicture))
				{
					echo '<img src="';
					echo 'backendfiles/showfile.php?id='.$lineProfilePicture['ProfilePictureID'].'&amp;type=T&amp;loc=Pro';
					echo '"/>';
				}
				else
				{
					echo '<img src="paulpaul.png" />';
				}
				if($edit)
				{
					echo '<input type="checkbox" name="delete" value="delete" />remove picture<br /><input type="file" name="profilepicture" />';
				}
				?>
    		</td>
    	</tr>
	<tr>
		<td style="width: 520px !important; width: 620px">
			<h2>Description</h2>
            <?php
            if($edit)
			{
				echo '<textarea name="description" rows="6" cols="60">'.$ThisDescription.'</textarea>';
            }
            else
            {
				echo $ThisDescription.'<br /><br />';
            }
?>
            <table>
            	<tr>
            		<td width="120">
                    <b>Email:</b><br/>
                    <b>Joined:</b><br/>
                    <b>LastLoggedIn</b><br />
            		</td>
                    <td>
                    </td>
            		<td>
                            <?php
			if($edit)
			{				
           		echo '<input type="text" name="email" value="'.$ThisEmail.'" />';
			}
			else
			{
				echo ((!empty($ThisEmail))?$ThisEmail:'Not Listed');
			}
		   
		   echo '
							<br />
    '.((!empty($ThisDateJoined))?$ThisDateJoined:'Not Listed').'
							<br />
'.((!empty($ThisLastLoggedIn))?$ThisLastLoggedIn:'Not Listed');
							?>
            		</td>
            	</tr>
            </table>
            <?php
			if($edit)
			{
				echo '<input type="submit" name="update" value="Update Profile" />
				<input name="cancel" type="submit" value="Cancel" /></form>';
			}
			?>
            </div>
		</td>
	</tr>
	<tr>
		<td valign="top" style="width: 520px !important; width: 620px;">
			<h2>Game Submittals</h2>
            <?php
$queryGame = 'SELECT GameID, Name, ProfileID, GameDescription, MinPlayers, MaxPlayers FROM Game WHERE ProfileID="'.$ThisProfileID.'" ORDER BY Name';
$resultGame = mysql_query($queryGame, $dblink) or die("Retrieve query failed: $queryGame ".mysql_error());
echo '<table>';
while($lineGame = mysql_fetch_assoc($resultGame))
{
	$GameID = $lineGame['GameID'];
	echo '<tr><td>
	</td>
	<td>
	<a href="GamePage.php?gid='.$GameID.'">'.$lineGame['Name'].'</a><br />
	<b>Players: </b>'.$lineGame['MinPlayers'].'-'.$lineGame['MaxPlayers'].'<br />
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
	echo '<br />'.$linePlatform['GameDescription'];
	echo '</td></tr>';
}
echo '</table>';
			?>
                    </td>
                    </tr>
                    </table>
<?php
include 'footer.php';
?>