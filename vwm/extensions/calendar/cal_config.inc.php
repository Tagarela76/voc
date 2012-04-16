<?php

# configuration file: adjust the '$calpath', your database-connection, then it works !
# after that you're free to adjust all other vars


############################################################################
#
# base dir (include ending slash !), on win use two '\\', on linux/unix just '/' */

$calpath = "/home/developer/Workspace/voc_src/vwm/extensions/calendar/";


############################################################################
#
# database connection 

$db = 'calendar';
$dbuser = 'root';
$dbpass = 'rootpass';
$dbhost = 'localhost';

mysql_connect($dbhost,$dbuser,$dbpass) or die("could not connect");
mysql_select_db("$db") or die("could not open database");

############################################################################
#
# Calendar admin authorisation
# 1 = yes, 0 = no

$calauth = 1;

############################################################################
#
# language 
#
# e  -> english
# n  -> dutch
# fr -> french
# g  -> german
# k  -> korean
# a  -> arabic
# es -> espa�ol
# i  -> italian

$language = 'e';

# don't change next 3 lines !
require $calpath."cal_languages/".$language.".inc.php";
require $calpath."cal_languages/".$language.".months.php";
require $calpath."cal_languages/".$language.".week.php";


############################################################################
#
# default view 
#
# this is the default you get when just surfing to index.php
#
# 0 = todayview
# 1 = weekview
# 2 = monthview

$caldefault = '2';


############################################################################
#
# (non) views
#
# set several things (url, search) on/off on the 'live' site
# 1 is on, 0 is off

$popupevent = '1'; // is event in popup-screen(1) or just url(0)
$popupeventheight = '400'; // height of the popup-screen
$popupeventwidth = '400';  // width of the popup-screen

$caleventapprove = '0'; // approve events given in by site-user; 0 = yes, 1 = no
$caleventadminapprove = '0'; // approve events by admin-user; 0 = yes, 1 = no

$addeventok = '1'; // 'add event - url' 
$viewcatsok = '1'; // 'view categories - url'
$viewdayok = '1';  // 'view by day - url'
$viewweekok = '1'; // 'view by week - url'
$viewcalok = '1';  // 'view month -  url'

$vieweventok = '1';     // search on view individual view
$searchcatsok = '1';    // search on overview of categories
$searchscatviews = '1'; // search on overview of events in 1 category
$searchdayok = '1';     // search on view events by day
$searchweekok = '1';    // search on view events by week
$searchmonthok = '1';   // search on view events by month

$viewtodaydate = '1';   // view today date at the top

############################################################################
#
# colors for the 'live site'

# background gcolor
$bgcolor = '#FFFFFF';

# vars for categories
# two colors because the <tr>'s alternate
$firstcatcolor = '#BBBBBB';
$secondcatcolor = '#DDDDDD';

# vars for event from one category 
# two colors, because the colors alternate
$firstcatevcolor = '#BBBBBB';
$secondcatevcolor = '#DDDDDD';

# vars for calendar-month-view
$tablewidth = '98%'; // width of table
$monthborder = '0'; // tableborder or not
$tdwidth = '14%'; // width of cell
$tdtopheight = '30'; // standard height of top cell
$tddayheight = '50'; // standar height of weekday-cell
$tdheight = '50'; // standard height of day-cell
$calcells = '1'; // cellspacing
$calcellp = '0'; // cellpadding 
$trtopcolor = '#CCCCCC'; // top <tr>
$calfontback = '+1'; // link previous month
$calfontasked = '+3'; // link asked month
$calfontnext = '+1'; // link next month
$sundaytopclr = '#BBBBBB'; // color sundayname in <tr>-top
$weekdaytopclr = '#DDDDDD'; // color weeknames
$sundayemptyclr = '#FFFFFF'; // color of sunday that isn't in month
$weekdayemptyclr = '#FFFFFF'; // color empty <td>
$todayclr = '#446699'; // color today
$sundayclr = '#BBBBBB'; // color calendarsunday
$weekdayclr = '#DDDDDD'; // color calendarweekday
?>
