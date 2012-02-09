<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Here There Be Games |Home|</title>
<link href="main css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.2.3.min.js"></script>
<script type="text/javascript" src="js/jquery.lavalamp.min.js"></script>
<!-- Optional -->
<script type="text/javascript" src="js/jquery.easing.min.js"></script>

<script type="text/javascript">
    $(function() { $(".lavaLamp").lavaLamp({ fx: "backout", speed: 700 })});
</script>
<style type="text/css">
#facebook {
	position:absolute;
	left:905px;
	top:915px;
	width:285px;
	height:81px;
	z-index:8;
}
#twitter {
	position:absolute;
	left:904px;
	top:1003px;
	width:286px;
	height:79px;
	z-index:9;
}
#Terms-Privacy {
	position:absolute;
	left:832px;
	top:1154px;
	width:225px;
	height:36px;
	z-index:10;
	color: #FFF;
	font-family: Georgia, "Times New Roman", Times, serif;
}
#search {
	position:absolute;
	left:900px;
	top:72px;
	width:337px;
	height:49px;
	z-index:11;
}
#login {
	position:absolute;
	left:965px;
	top:5px;
	width:194px;
	height:46px;
	z-index:12;
}
#headline {
	position:absolute;
	left:589px;
	top:211px;
	width:497px;
	height:250px;
	z-index:13;
}
#welcome {
	position:absolute;
	left:97px;
	top:274px;
	width:377px;
	height:297px;
	z-index:15;
	font-family: Georgia, "Times New Roman", Times, serif;
	color: #FFF;
	font-size: 16px;
}
#Recommend {
	position:absolute;
	left:190px;
	top:583px;
	width:399px;
	height:37px;
	z-index:16;
	font-size: 34px;
	font-weight: bold;
	font-family: Georgia, "Times New Roman", Times, serif;
	color: #282727;
}
#Reco1 {
	position:absolute;
	left:61px;
	top:634px;
	width:205px;
	height:407px;
	z-index:18;
}
#Reco2 {
	position:absolute;
	left:278px;
	top:634px;
	width:205px;
	height:407px;
	z-index:18;
}
#Reco3 {
	position:absolute;
	left:494px;
	top:634px;
	width:205px;
	height:407px;
	z-index:18;
}
#profile {
	position:absolute;
	left:1150px;
	top:5px;
	height:21px;
	/*z-index:14;*/
	font-family: Georgia, "Times New Roman", Times, serif;
}
</style>
</head>
<body>
<div id="FollowUs"></div>
<div id="facebook"><a href="http://www.facebook.com/"><img src="images/find-us-on-facebook_logo-gif.png" width="284" height="82" /></a></div>
<div id="twitter"><a href="http://twitter.com/"><img src="images/twitter-logo.jpg" width="284" height="82" /></a></div>
<div id="Terms-Privacy">Terms of Use - Privacy Policy</div>
<div id="search"><form action="Search.php" name="simple_bar" method="get">
  <table width="334" height="36" border="1" cellpadding="5">
    <tr>
      <td>
        <!--input type="hidden" name="dff_view" value="grid"-->
        Search:<input type="text" name="search" size="30" maxlength="50"><input type="submit" value="Find">
      </td>
    </tr>
  </table>
</form>
</div>
<div id="login"><img src="images/loginout.JPG" width="188" height="41" usemap="#Map" />
<map name="Map" id="Map">
<area shape="rect" coords="0,0,96,40" href="Login.php" />
<area shape="rect" coords="96,0,187,40" href="Registration.php" />
</map>
</div>
<div id="profile"><?php echo ((isset($_SESSION['ProfileID']) && isset($_SESSION['UserName']))?'<a href="ProfilePage.php?pid='.$_SESSION['ProfileID'].'">'.$_SESSION['UserName'].'</a>, 
<a href="Gamesubmittal.php">Submit</a>, <a href="backendfiles/Logout.php">Logout</a>':'you are not logged in'); ?></div>
<div id="headline"><img src="images/headlines.gif" width="504" height="360" /></div>
<!--div id="profile"><a href="Profile.html">User Profile</a></div-->
<div id="welcome">Welcome to Here There Be Games, the leading online games site, where you can   play a large range of free online games including action games, sports   games, puzzle games,  games for kids, flash games and   many more.</div>
<div id="Recommend">Recommended Games </div>
<div id="Reco1"><img src="images/recomen1.PNG" width="201" height="409" /></div>
<div id="Reco2"><img src="images/recomen2.PNG" width="202" height="406" /></div>
<div id="Reco3"><img src="images/recomen3.PNG" width="202" height="405" /></div>
<div id="wrapper">
  <div id="headerTop">
<div id="footer"></div>
<div id="middleNav"></div>
<span style="text-align: center"><!---- end headerTop ---->
<div id="navbar">
<div id="lavaWrapper">
  <ul class="lavaLamp">
    <li class="current"><a href="index.php">Home</a></li>
    <li><a href="Search.php?search=">GAMEs</a><!--a href="Downloads.html">DOWNLOADs</a--><a href="Gamesubmittal.php">SUBMIT</a></li>

    <li></li>

    <li><!--a href="About.html">About</a></li>
    <li><a href="Contact.html">Contact</a--></li>
  <li style="left: 0px; width: 83px; overflow: hidden;" class="back"><div class="left"></div></li></ul>
  <!-- end ul class lavalamp -->
<div id="logo"><a href="index.php"><img src="images/logo_gamesite.png" width="211" height="257" alt="logo" /></a></div>
</div> 
<p>
 
  </ul><!---- end el class lavalamp ---->
</p>
<p>&nbsp;</p>
</div><!---- end lavaWrapper ----><!---- end wrapper div ---->

</div>
</div>
<?php
include 'footer.php';
?>