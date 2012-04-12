<?
function translate($zin){

    switch ($zin) {

        case "Admin": $new = "ÇáãÔÑÝ";    break;
        case "cate": $new = "ÅÙåÇÑ ÍÓÈ ÇáÊÕäíÝ"; break;   
        case "day": $new = "ÅÙåÇÑ íæãí"; break;
        case "week": $new = "ÅÙåÇÑ ÃÓÈæÚí"; break;
        case "cal": $new = "ÇáÊÞæíã ÇáÔåÑí"; break;
        case "nocats": $new = "áÇÊæÌÏ ÊÕäíÝÇÊ "; break;
        case "addcat": $new = "ÃÖÝ ÊÕäíÝÇ"; break;
        case "cats": $new = "ÇáÊÕäíÝÇÊ"; break;
        case "addevent": $new = "ÃÖÝ ÍÏËÇ"; break; 
        case "outof": $new = "'ÃÍÏÇË ÞÏíãå"; break;
        case "eventitle": $new = "ÅÓã ÇáÍÏË"; break;
        case "description": $new = "æÕÝ ÇáÍÏË"; break;
        case "choosecat": $new = "ÅÎÊÑ ÊÕäíÝÇ"; break;
        case "selectyear": $new = "ÇáÓäå"; break;    
        case "selectmonth": $new = "ÇáÔåÑ"; break;
        case "selectday": $new = "Çáíæã"; break;
        case "bdate": $new = "ÇáÊÇÑíÎ"; break;
        case "notitle": $new = "íÌÈ ÅÏÎÇá ÅÓã ááÍÏË"; break;
        case "nodescription": $new = "íÌÈ ÅÏÎÇá æÕÝ ááÍÏË"; break;
        case "noday": $new = "íÌÈ Ãä ÊÎÊÇÑ íæãÇ"; break;
        case "nomonth": $new = "íÌÈ Ãä ÊÎÊÇÑ ÔåÑÇ"; break;
        case "noyear": $new = "íÌÈ Ãä ÊÎÊÇÑ ÚÇãÇ"; break;
        case "nocat": $new = "íÌÈ Ãä ÊÎÊÇÑ ÊÕäíÝÇ"; break;
        case "back": $new = "ÑÌæÚ"; break;
        case "nononapproved": $new = "áÇíæÌÏ ÃÍÏÇË áã íÊã ÅÚÊãÇÏåÇ"; break;
        case "nonapproved": $new = "ÃÍÏÇË áã íÊã ÅÚÊãÇÏåÇ: "; break;
        case "cat": $new = "ÇáÊÕäíÝ"; break;
        case "view": $new = "ÔÇåÏ ÇáÍÏË"; break;
        case "edit": $new = "ÚÏá ÇáÍÏË"; break;
        case "approve": $new = "ÅÚÊãÇÏ"; break;
        case "moreinfo": $new = "ÊÝÇÕíá"; break;   
        case "editcat": $new = "ÚÏá ÇáÊÕäíÝ"; break;
        case "delcat": $new = "ÍÐÝ ÇáÊÕäíÝ"; break;
        case "edit": $new = "ÊÚÏíá"; break;
        case "del": $new = "ÍÐÝ"; break;
        case "name": $new = "ÇáÅÓã"; break;
        case "update": $new = "ÊÍÏíË"; break;
        case "reallydelcat": $new = "åá ÃäÊ ãÊÃßÏ ãä ÍÐÝ ÇáÊÕäíÝ¿ ßá ÇáÃÍÏÇË ÇáãÑÊÈØÉ ÈåÐÇ ÇáÊÕäíÝ ÓíÊã ÈÇáÊÇáí ÍÐÝåÇ"; break;
        case "noback": $new = "Ãæå áÇ, ÇáÑÌÇÁ ÇáÚæÏå"; break;
        case "surecat": $new = "äÚã¡ ÅÍÐÝ ÇáÂä"; break;
        case "noevents": $new = "áÇÊæÌÏ ÃÍÏÇË"; break;
        case "numbevents": $new = "ÇáÃÍÏÇË Ýí ÇáÊÕäíÝ "; break;
        case "upevent": $new = "ÊÍÏíË ÇáÍÏË"; break;
        case "delev": $new = "ÍÐÝ ÇáÍÏË"; break;
        case "nooutofdate": $new = "áÇÊæÌÏ ÃÍÏÇË ÞÏíãå"; break;
        case "delalloodev": $new = "ÍÐÝ ÌãíÚ ÇáÃÍÏÇË ÇáÞÏíãå"; break;
        case "delevok": $new = "äÚã ÅÍÐÝ ÇáÍÏË"; break;
        case "delalloodevok": $new = "ÅÍÐÝ ÇáÌãíÚ"; break;
        case "prevm": $new = "ÇáÔåÑ ÇáÓÇÈÞ"; break;
        case "nextm": $new = "ÇáÔåÑ ÇáÊÇáí"; break;
        case "todaysdate": $new = "Çáíæã"; break;
        case "today": $new = "ÃÍÏÇË Çáíæã"; break;
        case "readmore": $new = "ÊÝÇÕíá"; break;
        case "nextday": $new = "Çáíæã ÇáÊÇáí"; break;
        case "prevday": $new = "Çáíæã ÇáÓÇÈÞ"; break;
        case "askedday": $new = "ÇáÊÇÑíÎ"; break;
        case "nextweek": $new = "ÇáÅÓÈæÚ ÇáÊÇáí"; break;
        case "prevweek": $new = "ÇáÅÓÈæÚ ÇáÓÇÈÞ"; break;
        case "weeknr": $new = "ÑÞã ÇáÅÓÈæÚ"; break;
        case "eventsthisweek": $new = " ãä "; break;
        case "till": $new = "æÍÊì "; break;
        case "thankyou": $new = "ÔßÑÇ áÅÖÇÝÊß ÍÏËÇ¡ ÓíÊã ÅÏÑÇÌå Ýí ÇáÊÞæíã ÞÑíÈÇ"; break;
	# here start the new not yet translated language vars
        case "disabled": $new = "This section has been disabled"; break;
	case "searchbutton": $new = "search"; break;
        case "searchtitle": $new = "Search"; break;
	case "onedate": $new = "One date"; break;
        case "moredates": $new = "More dates"; break;
	case "moredatesexplain": $new = "More dates: 'dd-mm-yyyy;dd-mm-yyyy' if day is one, type 01, same for month! without end-';' !"; break;
	case "email": $new = "email"; break;
        case "results": $new = "results"; break;
        case "noresults": $new = "No results"; break;
        case "logout": $new = "Log out"; break;
        case "users" : $new = "users"; break;
        case "userman": $new = "User management"; break;
        case "deluser": $new = "Delete user"; break;
        case "addnewuser": $new = "Add new user"; break;
        case "login": $new = "Login"; break;
        case "password": $new = "Password"; break;
        case "userwarning": $new = "Be sure to remember your password, you can't recover it !"; break;
        case "userdelok": $new = "Are you sure to delete this user ?"; break;
        default: $new = " ";    break;

    }
    return $new;
}
?>

