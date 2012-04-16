<?
include ("../versioncheck.inc.php");
include ("../cal_config.inc.php");

if (!isset($_GET['op']))
  $op = '';
else
  $op = $_GET['op'];

if ($op == "loginok"){
  $cookie = $_GET['cookie'];
  setcookie("extcallogin",$cookie,(time() + 3600));
  header("location: calendar.php");
}

if ($op == "logout"){
  setcookie("extcallogin","",(time() + 3600));
  header("location: cal_login.php");
}

?>

<html>
  <head><title>Extcalendar login</title></head>
<body>
<h3>Extcalendar login</h3>

<?

if ($op == "login"){

$login = $_POST['login'];
$password = $_POST['password'];

$crypt = "3xt-ca73ndar";
$cryptpas = crypt($crypt,$password);

$query = "select login,paswoord from calendar_admins where login='".$login."' AND paswoord='".$cryptpas."'";
$result = mysql_query($query);
$row = mysql_fetch_object($result);
if (!$row){
  echo translate("wronglogin");
  echo "<br><a href=javascript:history.back()>".translate("back")."</a>";
}
else{
  $cookie1 = $row->login;
  $cookie2 = $row->paswoord;
  $cookie = $row->login."|".$row->paswoord;
  echo "<meta http-equiv=\"refresh\" content=\"0;url=cal_login.php?op=loginok&cookie=$cookie\">";
}

}
else{

echo "<form action=cal_login.php?op=login method=post>";
echo translate("login")." <br><input type=text name=login><br>";
echo translate("password")." <br><input type=password name=password><br>";
echo "<input type=submit>";
echo "</form>";


}


?>
</body>
</html>
