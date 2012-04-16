<?
include ('cal_header.inc.php');
# today's date
if ($viewtodaydate == 1){
  $weekday = date ("w", mktime(12,0,0,$m,$d,$y));
  $weekday++;
  echo $week[$weekday]." ".$d." ".strtolower($maand[$m])." ".$y;
  echo "<br><br>";
}

/**************/
/* view event */
/**************/

function view($id){
global $language,$maand,$vieweventok;

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
if ($vieweventok == 1)
	search();
}

/*******************/
/* search function */
/*******************/

function search(){

echo "<form action=cal_search.php method=post>\n";
echo "<input type=text name=search>\n";
echo "<input type=submit value=\"".translate("searchbutton")."\">\n";
echo "</form>\n";
}

/*****************/
/* back function */
/*****************/

function back(){
  echo "<br><a href=javascript:history.back()>".translate("back")."</a>\n";
}


/**************************/
/* overview of categories */
/**************************/

function cats($firstcatcolor,$secondcatcolor){
global $viewcatsok,$searchcatsok;
if ($viewcatsok == 1){
$query = "select cat_id,cat_name from calendar_cat";
$result = mysql_query($query);
$rows = mysql_num_rows($result);

    // if no rows
    if ($rows == "0"){
        echo "<h3>".translate("nocats")."</h3>\n";
    }
    // show categorys
    else {
        echo "<h3>".translate("cats")."</h3>\n";
        echo "<table border=0 cellspacing=0 cellpadding=4>\n";
	$foo = '';
        while ($row = mysql_fetch_object($result)){
            $foo++ % 2 ? $color=$firstcatcolor : $color=$secondcatcolor;
            echo "<tr bgcolor=$color><td><li><a href=calendar.php?op=cat&id=".$row->cat_id.">".stripslashes($row->cat_name)."</a></tD></tr>\n";
        }
        echo "</table>\n";
    }
if ($searchcatsok == 1)
	search();
}
else{
echo translate("disabled");
}
}

/*******************************/
/* view events of one category */
/*******************************/

function cat($id,$firstcatevcolor,$secondcatevcolor){
global $language,$maand,$searchscatviews,$popupevent,$popupeventheight,$popupeventwidth;

$query = "select id,title,cat_name,day,month,year from events left join calendar_cat on events.cat=calendar_cat.cat_id where approved='1' and events.cat='$id' order by year ASC, month ASC, day ASC";

$result = mysql_query($query);
$rowname = mysql_fetch_object($result);
$rows = mysql_num_rows($result);
if (!$rows){

echo "<br><h3>".translate("noevents")."</h3>\n";

}

else {

echo "<h3>".translate("numbevents")." ".$rowname->cat_name."</h3>\n";

$result = mysql_query($query);
$foo = '';
while ($row = mysql_fetch_object($result)){

$foo++ % 2 ? $color=$firstcatevcolor : $color=$secondcatevcolor;

echo "<table border=1 bgcolor=$color cellspacing=0 cellpadding=4 width=\"100%\">\n";
echo "<tr><td>\n<li>";
if ($popupevent == 1)
 echo "<a href=\"#\" onclick=\"MM_openBrWindow('cal_popup.php?op=view&id=".$row->id."','Calendar','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=".$popupeventwidth.",height=".$popupeventheight."')\">";
else
  echo "<a href=calendar.php?op=view&id=".$row->id.">";
echo "<b>".stripslashes($row->title)."</b></a> ".translate("op")." ".$row->day." ".$maand[$row->month]." ".$row->year."\n";
echo "</td></tr>\n";
echo "</table>\n";

}

}
if ($searchscatviews == 1)
	search();
}

/**********************/
/* add event: the form */
/**********************/

function eventform(){
global $language,$addeventok,$maand;
if ($addeventok == 1){
echo "<h3>".translate("addevent")."</h3>";
echo "<form action=calendar.php?op=addevent method=post>\n";
echo translate("eventitle")."<br>\n";
echo "<input type=text name=title><br>\n";
echo translate("description")."<br>\n";
echo "<textarea name=description cols=50 rows=7></textarea><br>\n";
echo translate("email")."<br>\n";
echo "<input type=text name=email><br>\n";
echo "URL<br>\n";
echo "<input type=text name=url><br>\n";
// get the categories
echo "<select name=cat>\n\t<option value=0>".translate("choosecat")."\n";
$query = "select cat_id,cat_name from calendar_cat";
$result = mysql_query($query);
    while ($row = mysql_fetch_object($result)){
        echo "\t<option value=".$row->cat_id.">".$row->cat_name."\n";
    }

echo "</select>\n<br>\n";

// get days
echo translate("bdate")."<br>\n";
echo "<select name=bday>\n\t<option value=0>".translate("selectday")."\n";
for ($i = 1;$i<=31;$i++){
echo "\t<option>$i\n";
}
echo "</select>&nbsp;&nbsp;\n";

// get months
echo "<select name=bmonth>\n\t<option value=0>".translate("selectmonth")."\n";
for($i=1;$i<13;$i++){
 echo "\t<option value=".$i.">".ucfirst($maand[$i])."\n";
}
echo "</select>&nbsp;&nbsp;\n";

// get year and give 3 years more to select
echo "<select name=byear>\n\t<option value=0>".translate("selectyear")."\n";
$year = date("Y");
for ($i=0;$i<=4;$i++){
echo "\t<option>$year\n";
$year += 1;
}
echo "</select><br>\n";

echo "<input type=submit value=\"".translate("addevent")."\">\n<br>\n";

echo "<form>\n";
}
else{
echo translate("disabled");
}
}

/*************/
/* add event */
/*************/

if ($op == "addevent"){
  $title = $_POST['title'];
  $description = $_POST['description'];
  $email = $_POST['email'];
  $url = $_POST['url'];
  $cat = $_POST['cat'];
  $bday = $_POST['bday'];
  $bmonth = $_POST['bmonth'];
  $byear = $_POST['byear'];
}

function addevent($title,$description,$email,$url,$cat,$bday,$bmonth,$byear){
global $caleventapprove;

if (!$title) { echo translate("notitle"); back(); }
elseif (!$description) { echo translate("nodescription"); back(); }
elseif (!$cat) { echo translate("nocat"); back(); }
elseif (!$bday) { echo translate("noday"); back(); }
elseif (!$bmonth) { echo translate("nomonth"); back(); }
elseif (!$byear) { echo translate("noyear"); back(); }

else {

$title = addslashes($title);
$description = addslashes(nl2br($description));
$url = str_replace("http://","",$url);

$approve = $caleventapprove;
$query = "insert into events values('','$title','$description','$url','$email','$cat','$bday','$bmonth','$byear','$approve')";
#echo $query;
$result = mysql_query($query);
if ($caleventapprove == 0)
  echo translate("thankyou");
else
  echo "<meta http-equiv=\"refresh\" content=\"0;URL=calendar.php\">";
}

}


/***********************/
/* view events per day */
/***********************/

function day($ask,$da,$mo,$ye,$next,$prev){
global $maand,$week,$language,$m,$d,$y,$viewdayok,$searchdayok,$popupevent,$popupeventwidth,$popupeventheight;

if (!isset($yda))
  $yda = '';

if ($viewdayok == 1){
// als er geen dag is, dan is het vandaag
if (!$da){
    $da = $d;
    $mo = $m;
    $ye = $y;
}

$we = mktime(0,0,0,$mo,$da,$ye);
$we = strftime("%w",$we);
$we++;
echo "<h3>".translate("askedday").": ".$week[$we]." ".$da." ".$maand[$mo]." ".$ye."</h3>";

// eerst alle items zoeken (anders serieuze mix-up van vars)
$query = "select id,title,description,url,cat_name,day,month,year from events left join calendar_cat on events.cat=calendar_cat.cat_id where day='$da' and month='$mo' and year='$ye' and approved='1' order by title ASC";
$result = mysql_query($query);

// als ask = volgende dag...
if (!$ask || $ask == "nd"){
    // bepaal maand en jaar  voor previous (moet nu al, anders mix up !)
    $ymo = $mo;
    $yy = $ye;
    // als next is, optellen
    if ($next){
        $ok = 86400*$next;
        $da = date("j",time()+ $ok);
        $next++;
    }
    // geen next, dag is vandag, dus maar 1 keer vermenigvuldigen
    else {
        $da = date("j",time()+86400);
        $next = '2';
    }

    // vars voor volgende dag
    // nieuwe dag = 1, maand stijgt
    if ($da == "1")
        $mo++;
    // nieuwe maand = dertien, jaar stijgt
    if ($mo == "13"){
        $mo = '1';
        $ye += 1;
    }
    // vars voor vorige dag (als die er is natuurlijk)
    // dag
    if ($prev){
        if ($prev != "O"){
            $ok = 86400*$prev;
            $yda = date("j",time()+$ok);
            $prev++;
        }
        else {
            $yda = date("j");
            $prev = '1';
        }
    }
    else {
        $prev = 'O';
    }
    // nieuwe dag = 2, maand stijgt
    if ($da == "2")
        $ymo--;
    if ($ymo == "0")
        $ymo = '12';
    //als nieuwe dag gelijk aan 2 en nieuwe maand gelijk aan 1: jaar +1 pd-vars +1
    if ($da == "2" &&  $ymo == "1"){
        $yy -= 1;
    }
    // dag 31 & maand 12 = jaar beneden
    if ($yda == "31" && $ymo == "12")
        $yy -= 1;

    // vorige dag link, als next 2 is = vandaag op scherm, dus geen vorige dag (what's the use eh :)
    if ($next != "2")
        echo "<a href=\"calendar.php?op=day&ask=pd&da=$yda&mo=$ymo&ye=$yy&next=$next&prev=$prev\"><== ".translate("prevday")."</a> - ";
    // link naar volgende dag
    echo "<a href=\"calendar.php?op=day&ask=nd&da=$da&mo=$mo&ye=$ye&next=$next&prev=$prev\">".translate("nextday")." ==></a><br><br>";

}

// als ask = vorige dag ...
if ($ask == "pd"){

    // bepaal maand en jaar  voor previous (moet nu al, anders mix up !)
    $ymo = $mo;
    $yy = $ye;
    // next -> optellen
    $next -= 2;
    $ok = 86400*$next;
    $da = date("j",time()+ $ok);
    $next++;
    // vars voor volgende dag
    // nieuwe dag = 1, maand daalt
    if ($da == "2")
        $mo--;
    // nieuwe maand = dertien, jaar daalt
    if ($mo == "0"){
        $mo = '12';
        $ye -= 1;
    }
    if ($da == "2" && $mo == "13")
        $mo = "1";
    // vars voor vorige dag (als die er is natuurlijk)
    // dag
    $prev -=2;
    if ($prev == "0")
        $prev == "1";
    $ok = 86400*$prev;
    $yda = date("j",time()+$ok);
    $prev++;
    // nieuwe dag = 2, maand daalt
    if ($da == "2"){
        $ymo--;
        $mo++;
    }
    if ($da == "1")
        $mo++;
    if ($ymo == "13"){
        $ymo = '1';
        $yy -= 1;
    }
    if ($ymo == "0")
        $ymo = '12';
    // nieuwe maand = twaalf, jaar daalt
    if ($yda == "31" && $ymo == "12"){
        $yy -= 1;
        $mo = '1';
        $ye += 1;
    }
    if ($yda == "30" && $ymo == "12"){
        $mo = '1';
        $ye += 1;
    }
    // als next gelijk is aan twee, dan is prev = O
    if ($next == "2")
        $prev ='O';

    // vorige dag link, als next 2 is = vandaag op scherm, dus geen vorige dag (what's the use eh :)
    if ($next != "2")
        echo "<a href=\"calendar.php?op=day&ask=pd&da=$yda&mo=$ymo&ye=$yy&next=$next&prev=$prev\"><== ".translate("prevday")."</a> - ";
    // link naar volgende dag
    echo "<a href=\"calendar.php?op=day&ask=nd&da=$da&mo=$mo&ye=$ye&next=$next&prev=$prev\">".translate("nextday")." ==></a><br><br>";

}
// beeld de zaken af van de gevraagde dag
  while ($row = mysql_fetch_object($result)){
    echo "<li><b><U>".$row->title."</u></b><br>";
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
if ($searchdayok == 1)
	search();
}
else{
 echo translate("disabled");
}
}

/************************/
/* view events per week */
/************************/

function week($week,$date){
global $maand,$week,$language,$m,$d,$y,$ld,$fd,$viewweekok,$searchweekok,$popupevent,$popupeventwidth,$popupeventheight;

if ($viewweekok == 1){

if (!$date){
$year = $y;
$month = $m;
$day = $d;
}
else{
$year = substr($date,0,4);
$month = substr($date,5,2);
$day = substr($date,8,2);
}

// weeknummer
function weekNumber($dag,$maand,$jaar)
{
    $a = (14-$maand)/12;
    settype($a,"integer");
    $y = $jaar+4800-$a;
    settype($y,"integer");
    $m = $maand + 12*$a - 3;
    settype($m,"integer");
    $J = $dag + (153*$m+2)/5 + $y*365 + $y/4 - $y/100 + $y/400 - 32045;
    $d4 = ($J+31741 - ($J % 7)) % 146097 % 36524 % 1461;
    $L = $d4/1460;
    $d1 = (($d4-$L) % 365) + $L;
    $WeekNumber = ($d1/7)+1;
    settype($WeekNumber,"integer");
    return $WeekNumber;
}

$deesweek = mktime(0,0,0,date("d"), date("m"), date("Y"));
$weeknummer = weekNumber($day,$month,$year);
$laatsteweek = ($weeknummer + 10);
if ($laatsteweek > 52){
 $laatsteweek = $laatsteweek - 52;
}

// eerste dag van de week
function firstDayOfWeek($year,$month,$day){
 global $fd;
 $dayOfWeek=date("w");
 $sunday_offset=$dayOfWeek * 60 * 60 * 24;
 $fd = date("Y-m-d", mktime(0,0,0,$month,$day+1,$year) - $sunday_offset);
 return $fd;
}
firstDayOfWeek($year,$month,$day);

// laatste dag van de week
function lastDayOfWeek($year,$month,$day){
 global $ld;
 $dayOfWeek=date("w");
 $saturday_offset=(6-$dayOfWeek) * 60 * 60 * 24 ;
 $ld  = date("Y-m-d", mktime(0,0,0,$month,$day+1,$year) + $saturday_offset);
 return $ld;
}
lastDayOfWeek($year,$month,$day);

if (($date) && ($date != date("Y-m-d"))){
echo "<a href=calendar.php?op=week&date=".date("Y-m-d", mktime(0,0,0,$month,$day-7,$year))."><== ".translate("prevweek")."</a> - ";
}
echo "<a href=calendar.php?op=week&date=".date("Y-m-d", mktime(0,0,0,$month,$day+7,$year)).">".translate("nextweek")." ==> </a>";

// zin met datum begin van weeknummer en datum eind weeknummer
echo "<br><br>".translate("eventsthisweek");
$fdy = substr($fd,0,4);
$fdm = substr($fd,5,2);
if (substr($fdm,0,1) == "0"){
 $fdm = str_replace("0","",$fdm);}
$fdd = substr($fd,8,2);
echo $fdd." ".$maand[$fdm]." ".$fdy;
echo " ".translate("till")." ";
$ldy = substr($ld,0,4);
$ldm = substr($ld,5,2);
if (substr($ldm,0,1) == "0"){
 $ldm = str_replace("0","",$ldm);}
$ldd = substr($ld,8,2);
echo $ldd." ".$maand[$ldm]." ".$ldy;
echo " (".translate("weeknr")." : ".$weeknummer.")";

// en nu de evenementen eruit halen :)
$ld = date("Y-m-d", mktime(0,0,0,$ldm,$ldd+1,$ldy));
echo "<br><br>";
while ($fd != $ld){
$fdy = substr($fd,0,4);
$fdm = substr($fd,5,2);
if (substr($fdm,0,1) == "0"){
 $fdm = str_replace("0","",$fdm);}
$fdd = substr($fd,8,2);
$query = "select id,title,description,url,cat_name,day,month,year from events left join calendar_cat on events.cat=calendar_cat.cat_id where day='$fdd' and month='$fdm' and year='$fdy' and approved='1' order by title ASC";
//echo $query."<br>";
$result = mysql_query($query);
   while ($row = mysql_fetch_object($result)){
    echo "<li><b><U>".stripslashes($row->title)."</u></b><font size=-1> (".$row->day." ".$row->month." ".$row->year.")</font><br>";
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
$fd = date("Y-m-d", mktime(0,0,0,$fdm,$fdd+1,$fdy));
}
if ($searchweekok == 1)
	search();
}
else{
  echo translate("disabled");
}
}

/*****************/
/* view calender */
/*****************/

function cal($month,$year,$monthborder,$calcells,$calcellp,$tablewidth,$trtopcolor,$calfontback,$calfontasked,$calfontnext,$sundaytopclr,$weekdaytopclr,$sundayemptyclr,$weekdayemptyclr,$todayclr,$sundayclr,$weekdayclr){
global $maand,$week,$language,$m,$d,$y,$tdwidth,$tdtopheight,$tddayheight,$tdheight,$viewcalok,$searchmonthok,$popupevent,$popupeventwidth,$popupeventheight;

if ($viewcalok == 1){

// previous month
$pm = $month;
if ($month == "1")
    $pm = "12";
else
    $pm--;
// previous year
$py = $year;
if ($pm == "12")
    $py--;

// next month
$nm = $month;
if ($month == "12")
    $nm = "1";
else
    $nm++;
// next year
$ny = $year;
if ($nm == 1)
    $ny++;    

// get month we want to see
$askedmonth = $maand[$month];
$askedyear = $year;
$firstday = date ("w", mktime(12,0,0,$month,1,$year));
$firstday++;
// nr of days in askedmonth
$nr = date("t",mktime(12,0,0,$month,1,$year));
echo "<table border=$monthborder cellspacing=$calcells cellpadding=$calcellp >";
    echo "<tr bgcolor=$trtopcolor>";
        echo "<td align=center colspan=7 height=$tdtopheight>";
        if ($month != date("n") || $year != date("Y")){
        echo "<font size=$calfontback><a href=calendar.php?op=cal&month=".$pm."&year=".$py.">  <= ".$maand[$pm]." - ".$py."</a></font>"; }
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=$calfontasked>".$askedmonth." ".$askedyear."</font>";
        echo "&nbsp;&nbsp;&nbsp;<font size=$calfontnext><a href=calendar.php?op=cal&month=".$nm."&year=".$ny.">".$maand[$nm]." - ".$ny." => </a></font></td>";
    echo "</tr>";
    echo "<tr>";
        // make the days of week, consisting of seven <td>'s (=days)
        for ($i=1;$i<=7;$i++){
            echo "<td align=center width=$tdwidth height=$tddayheight ";
            if ($i == 1){
                echo "bgcolor=$sundaytopclr>".$week[$i]."</td>"; // sunday
	    }
            else{    
                echo "bgcolor=$weekdaytopclr>".$week[$i]."</td>"; // rest of week
	   }
        }
    echo "</tr>";
        // begin the days
        for ($i=1;$i<$firstday;$i++){
	    echo "<td height=$tdheight ";
                if ($i == "1"){
                    echo "bgcolor=$sundayemptyclr ";
		}
                else{
                        echo "bgcolor=$weekdayemptyclr ";
		}
            echo ">&nbsp;</td>";
        }
        $a=0;
        for ($i=1;$i<=$nr;$i++){
            echo "<td height=$tdheight ";
            if ($i == $d && $month == $m && $year == $y){ // higlight today's day
                echo "bgcolor=$todayclr ";
            }
	    if (($i == (9-$firstday)) or ($i == (16-$firstday)) or ($i == (23-$firstday)) or ($i == (30-$firstday)) or ($i == (37 - $firstday))){                
	       echo "bgcolor=$sundayclr ";
            }

            else{
              echo "bgcolor=$weekdayclr ";
            }
            echo " valign=top><b>".$i."</b>";
            // now get eventual events on $i 
                $query = "select id,title from events left join calendar_cat on events.cat=calendar_cat.cat_id where day='$i' and month='$month' and year='$year' and approved='1' order by day,month,year ASC";
               
				$result = mysql_query($query);
                    while ($row = mysql_fetch_object($result)){
			echo "<li>";
			if ($popupevent == 1)
			  echo "<a href=\"#\" onclick=\"MM_openBrWindow('cal_popup.php?op=view&id=".$row->id."','Calendar','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=".$popupeventwidth.",height=".$popupeventheight."')\">";
			else
			  echo "<a href=calendar.php?op=view&id=".$row->id.">";
                        echo stripslashes($row->title)."</a>";

                    }

            echo "</td>";
            // closing <tr> voor end of week
            $a++;
            if (($i == (8-$firstday)) or ($i == (15-$firstday)) or ($i == (22-$firstday)) or ($i == (29-$firstday)) or ($i == (36 - $firstday))){
            echo "</tr><tr>";
            $a = 0;
            }
        }
        // ending stuff (making 'white' td's to fill table
        if ($a != 0){
        $last = 7-$a;
            for ($i=1;$i<=$last;$i++){
                echo "<td bgcolor=$weekdayemptyclr>&nbsp;</td>";
            }
        }
    echo "</tr>";
echo "</table>";
echo "<table>";
echo "<tr><td bgcolor=$todayclr width=5 height=5 align=left>&nbsp;</td><td align=left> = ".translate("todaysdate")."</td></tr>";
echo "</table><br>";
if ($searchmonthok == 1)
	search();
}
else{
  echo translate("disabled");
}
}


switch ($op){
    
    // overview of category
    case"cats":{
       cats($firstcatcolor,$secondcatcolor);
    break;
    }

    // overview of one cat
    case"cat":{
        cat($id,$firstcatevcolor,$secondcatevcolor);
    break;
    }

    // add event form
    case"eventform":{
       eventform();
    break;
    }

    // add event
    case "addevent":{
        addevent($title,$description,$email,$url,$cat,$bday,$bmonth,$byear);
    break;
    }

    // view details of event
    case "view":{
        view($id);
    break;
    }
    

    // view per day 
    case"day":{
        day($ask,$da,$mo,$ye,$next,$prev);
    break;
    }

    // view per week 
    case"week":{
        week($week,$date);
    break;
    }

    // view cal per month
    case"cal":{
        cal($month,$year,$monthborder,$calcells,$calcellp,$tablewidth,$trtopcolor,$calfontback,$calfontasked,$calfontnext,$sundaytopclr,$weekdaytopclr,$sundayemptyclr,$weekdayemptyclr,$todayclr,$sundayclr,$weekdayclr);
    break;
    }
    
    // default: bar, and show new submissions
    default:{
	if ($caldefault == 0)
	        day($ask,$da,$mo,$ye,$next,$prev);
	if ($caldefault == 1)
		week($week,$date);
	if ($caldefault == 2){
		if (!$month)
			$month = $m;
		if (!$year)
			$year = $y;
        	cal($month,$year,$monthborder,$calcells,$calcellp,$tablewidth,$trtopcolor,$calfontback,$calfontasked,$calfontnext,$sundaytopclr,$weekdaytopclr,$sundayemptyclr,$weekdayemptyclr,$todayclr,$sundayclr,$weekdayclr);
	}
    break;
    }
}

include ('cal_footer.inc.php');
?>

