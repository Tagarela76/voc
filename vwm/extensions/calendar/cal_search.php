<?
include ('versioncheck.inc.php');
include ('cal_header.inc.php');

# search title
echo "<h3>".translate("searchtitle")."</h2>";

if (!isset($_POST['search']))
  $search = '';
else
  $search = $_POST['search'];

if (!$search){
        echo translate("noresults");
}
elseif (strlen($search) < 3){
        echo translate("noresults");
}
else {
   $query = "select id,title,description,url,cat_name,day,month,year from events left join calendar_cat on events.cat=calendar_cat.cat_id where title like '%$search%' OR description like '%$search%' AND approved = '1' order by year ASC, month ASC, day ASC";
   $result = mysql_query($query);
   $rows = mysql_num_rows($result);
  
   if ($rows == 0)
	echo translate("noresults");
   else{
    echo "<h3>$rows ".translate("results")."</h3>\n";
    while ($row = mysql_fetch_object($result)){
      echo "<li><b><U>".$row->title." (".$row->day ." ".$maand[$row->month]." ".$row->year.")</u></b><br>";
      echo translate("cat")." : ".$row->cat_name."<br>";
      $de = str_replace("<br>","",$row->description);
      $de = str_replace("<br />","",$row->description);
      echo substr(stripslashes($de),0,100)." ...";
      echo "<br>";
      if ($popupevent == 1)
        echo "<a href=\"#\" onclick=\"MM_openBrWindow('cal_popup.php?op=view&id=".$row->id."','Calendar','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=".$popupeventwidth.",height=".$popupeventheight."')\">";
      else
        echo "<a href=calendar.php?op=view&id=".$row->id.">";
      echo translate("readmore")."</a>";
      echo "<br><br>";
    }
  }
}
include ('cal_footer.inc.php');
?>
