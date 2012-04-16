<?
include ("../cal_config.inc.php");

if ($calauth == "1"){
  if (isset($_COOKIE["extcallogin"])){
    $clogin = $_COOKIE["extcallogin"];
    $clogin = explode("|",$clogin);
    $callogin = $clogin[0] ;
    $calpass = $clogin[1] ;
    $query = "select login,paswoord from calendar_admins where login='".$callogin."' AND paswoord='".$calpass."'";
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);
    if (!$row)
	header ("location: cal_login.php");
    else
	header ("location: calendar.php");
  }
  else
    header ("location: cal_login.php");
}
else
  header ("location: cal_login.php?op=loginok&cookie=login|ok");
?>
