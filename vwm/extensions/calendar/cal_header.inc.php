<?
include ("versioncheck.inc.php");
require "cal_config.inc.php";
?>
<!--
<html>
<head>
  <title>Calendar</title>
-->
<!-- javascript pop-up -->
     <script language="JavaScript">
            <!--
            function MM_openBrWindow(theURL,winName,features) { //v2.0
              window.open(theURL,winName,features);
	    }
	    //-->
	</script>

<link rel="stylesheet" href="<?$_SERVER['DOCUMENT_ROOT']?>/voc_src/vwm/extensions/calendar/css/master.css" type="text/css" media="screen" charset="utf-8" />	
<!--
</head>
<body bgcolor=<?echo$bgcolor?>>
-->
<?
# some settings of vars
if (!isset($_GET['op']))
  $op = '';
else
  $op = $_GET['op'];
if (!isset($_GET['month']))
  $month = '';
else
  $month = $_GET['month'];
if (!isset($_GET['year']))
  $year = '';
else
  $year = $_GET['year'];
if (!isset($_GET['date']))
  $date = '';
else
  $date = $_GET['date'];
if (!isset($_GET['ask']))
  $ask = '';
else
  $ask = $_GET['ask'];
if (!isset($_GET['da']))
  $da = '';
else
  $da = $_GET['da'];
if (!isset($_GET['mo']))
  $mo = '';
else
  $mo = $_GET['mo'];
if (!isset($_GET['ye']))
  $ye = '';
else
  $ye = $_GET['ye'];
if (!isset($_GET['next']))
  $next = '';
else
  $next = $_GET['next'];
if (!isset($_GET['prev']))
  $prev = '';
else
  $prev = $_GET['prev'];
if (!isset($_GET['id']))
  $id = '';
else
  $id = $_GET['id'];

# navbar at the top
$m = date("n");
$y = date("Y");
$d = date("j");

if ($addeventok == 1){
if ($op == "addevent"){echo "<b>";}
if ($op == "eventform"){echo "<b>";}
echo ":: <a href=calendar.php?op=eventform>".translate("addevent")."</a>";
if ($op == "eventform"){echo "</b>";}
if ($op == "addevent"){echo "</b>";}
}
if ($viewcatsok == 1){
if ($op == "cats"){ echo "<b>"; }
echo " :: <a href=calendar.php?op=cats>".translate("cate")."</a>";
if ($op == "cats"){ echo "</b>"; }
}
if ($viewdayok == 1){
if ($op == "day"){ echo "<b>"; }
echo " :: <a href=calendar.php?op=day>".translate("day")."</a>";
if ($op == "day"){ echo "</b>"; }
}
if ($viewweekok == 1){
if ($op == "week"){ echo "<b>"; }
echo " :: <a href=calendar.php?op=week>".translate("week")."</a>";
if ($op == "week"){ echo "</b>"; }
}

if ($viewcalok == 1){
if ($op == "cal"){ echo "<b>"; }
echo " :: <a href=calendar.php?op=cal&month=$m&year=$y>".translate("cal")."</a>";
if ($op == "cal"){ echo "</b>"; }
}
echo "<br><br>";
?>
