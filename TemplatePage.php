<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
$PageName = 'Here There Games ';
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Here There Be Games |Profile|</title>
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
#profile {
	position:absolute;
	left:1206px;
	top:0px;
	width:89px;
	height:21px;
	z-index:14;
	font-family: Georgia, "Times New Roman", Times, serif;
}
</style>
<!--div id="FollowUs"></div>
<div id="facebook"><img src="images/find-us-on-facebook_logo-gif.png" width="284" height="82" /></div>
<div id="twitter"><img src="images/twitter-logo.jpg" width="284" height="82" /></div>
<div id="Terms-Privacy">Terms of Use - Privacy Policy</div-->
<div id="search"><form name="simple_bar" method="get" action="Search.php">
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
</map></div>
<div id="profile"><?php echo ((isset($_SESSION['ProfileID']) && isset($_SESSION['UserName']))?'<a href="ProfilePage.php?pid='.$ProfileID.'">'.$UserName.'</a>, 
<a href="Gamesubmittal.php">Submit</a>, <a href="backendfiles/Logout.php">Logout</a>':'you are not logged in'); ?></div>
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
<div id="middleNav" align="center">