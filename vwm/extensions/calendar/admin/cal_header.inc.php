<?php
require "../cal_config.inc.php";

  if (isset($_COOKIE["extcallogin"])){
    $clogin = $_COOKIE["extcallogin"];
    $clogin = explode("|",$clogin);
    $callogin = $clogin[0] ;
    $calpass = $clogin[1] ;
    $row = 1;
    if ($calauth == "1"){
      $query = "select login,paswoord from calendar_admins where login='".$callogin."' AND paswoord='".$calpass."'";
      $result = mysql_query($query);
      $row = mysql_fetch_object($result);
    }
    if (!$row)
        header ("location: cal_login.php");
  }
  else
    header ("location: cal_login.php");

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
if (isset($_GET['userid'])){
  $userid = $_GET['userid'];
}

?>
<html>
<head><title>Calendar : admin</title>
    <script language="JavaScript">
        <!--
	function confirmdelete(){
                var agree=confirm("<?echo translate("userdelok");?>");
                if (agree)
                return true ;
                else
                return false ;
                }
	//-->
     </script>
</head>
<body bgcolor=#FFFFFF>
