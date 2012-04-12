<?
include('versioncheck.inc.php');
require ('cal_config.inc.php');
$id = $_GET['id'];
?>
<html>
<head>
 <title>Event</title>
</head>
<body bgcolor=<?echo$bgcolor?>

<?

$query = "select id,title,cat_name,description,day,month,year,approved,url,email from events left join calendar_cat on events.cat=calendar_cat.cat_id where id='$id'";
$result = mysql_query($query);
$row = mysql_fetch_object($result);
echo "<h3>".stripslashes($row->title)."</h3>\n";
echo "<li>".translate("bdate")." : ".$row->day ." ".$maand[$row->month]." ".$row->year."\n";
echo "<li>".translate("cat")." : ".$row->cat_name."\n";
echo "<li>".translate("description")."<br>\n";
echo stripslashes($row->description);
echo "<br><br>\n";
if ($row->email)
        echo "<li>".translate("email")." : <a href=mailto:".$row->email.">".$row->email."</a>\n";
if ($row->url){
echo "<li>".translate("moreinfo")." : <a href=http://".$row->url." target=_blank>".$row->url."</a>\n";
}


?>

</body>
</html>
