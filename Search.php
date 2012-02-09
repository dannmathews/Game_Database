<?php
session_start();
require_once 'backendfiles/dbconfig.php';
if(isset($_SESSION['ProfileID']))
{
	$ProfileID = $_SESSION['ProfileID'];
	$UserName = $_SESSION['UserName'];
}
else
{
	$UserName = 'guest';
	$ProfileID = 0;
}
include 'TemplatePage.php';
?>

<title>Search</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type="text/javascript">
function LoadPage(url) {
    $.get(url, function(data) {
      $('#list').html(data);
    });
    return false;
}

$(document).ready(function() {
//    LoadPage('SearchResultList.php');
  });
</script>
</head>
<body>
<h1 align="center">Search</h1>
<?php
$search = $_GET['search'];
echo '<div id="list">';
include 'SearchResultList.php';
echo '</div>';
include 'footer.php';
?>