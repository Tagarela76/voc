<?
function translate($zin){

    switch ($zin) {
        
        case "Admin": $new = "Administration";    break;
        case "cate": $new = "Browse by categories"; break;
        case "day": $new = "Browse by day"; break;
        case "week": $new = "Browse by week"; break;
        case "cal": $new = "This months calendar"; break;
        case "nocats": $new = "No categories yet"; break;
        case "addcat": $new = "Add category"; break;
        case "cats": $new = "Categories"; break;
        case "addevent": $new = "Add event"; break;
        case "outof": $new = "Historical items"; break;
        case "eventitle": $new = "Event title"; break;
        case "description": $new = "Even description"; break;
        case "choosecat": $new = "Choose category"; break;
        case "selectyear": $new = "Year"; break;
        case "selectmonth": $new = "Month"; break;
        case "selectday": $new = "Day"; break;
        case "bdate": $new = "Date"; break;
        case "notitle": $new = "You must give an event title !"; break;
        case "nodescription": $new = "You must give an event description"; break;
        case "noday": $new = "You must select a day !"; break;
        case "nomonth": $new = "You must select a month !"; break;
        case "noyear": $new = "You must select a year !"; break;
        case "nocat": $new = "You must select a category !"; break;
        case "back": $new = "Back"; break;
        case "nononapproved": $new = "There are no events needing approval at this time"; break;
        case "nonapproved": $new = "Evens requiring approval : "; break;
        case "cat": $new = "Category"; break;
        case "view": $new = "View event"; break;
        case "edit": $new = "Edit event"; break;
        case "approve": $new = "Approve"; break;
        case "moreinfo": $new = "More info"; break;
        case "editcat": $new = "Edit category"; break;
        case "delcat": $new = "Delete category"; break;
        case "edit": $new = "Edit"; break;
        case "del": $new = "Delete"; break;
        case "name": $new = "Name"; break;
        case "update": $new = "Update"; break;
        case "reallydelcat": $new = "Are you sure to remove this category ? All events associated with this category will be permanently deleted !"; break;
        case "noback": $new = "Oops, no, go back !"; break;
        case "surecat": $new = "Yes, delete it now !"; break;
        case "noevents": $new = "No events"; break;
        case "numbevents": $new = "Event in category "; break;
        case "upevent": $new = "Update event"; break;
        case "delev": $new = "Delete event"; break;
        case "nooutofdate": $new = "No out-of-date events."; break;
        case "delalloodev": $new = "Delete all out-of-date events"; break;
        case "delevok": $new = "Yes, delete event !"; break;
        case "delalloodevok": $new = "Delete them all !"; break;
        case "prevm": $new = "Previous month"; break;
        case "nextm": $new = "Next month"; break;
        case "todaysdate": $new = "Today"; break;
        case "today": $new = "Events today"; break;
        case "readmore": $new = "Read more"; break;
        case "nextday": $new = "Next day"; break;
        case "prevday": $new = "Previous day"; break;
        case "askedday": $new = "Asked day"; break;
        case "nextweek": $new = "Next week"; break;
        case "prevweek": $new = "Previous week"; break;
        case "weeknr": $new = "week number"; break;
        case "eventsthisweek": $new = "Events from "; break;
        case "till": $new = "till"; break;
        case "thankyou": $new = "Thank you for entering an event, it will apear shortly"; break;
        case "op": $new = "on"; break;
	# here start the new not yet translated language vars
        case "disabled": $new = "This section has been disabled"; break;
	case "searchbutton": $new = "search"; break;
	case "searchtitle": $new = "Search"; break;
	case "onedate": $new = "One date"; break;
        case "moredates": $new = "More dates"; break;
	case "moredatesexplain": $new = "More dates: 'dd-mm-yyyy;dd-mm-yyyy' if day is one, type 01, same for month! without end-';' !"; break;
	case "email": $new = "Email"; break;
	case "results": $new = "results"; break;
	case "noresults": $new = "No results"; break;
        case "wronglogin": $new = "Something wrong with login or password"; break;
        case "userman": $new = "User management"; break;
        case "users": $new = "Users"; break;
        case "logout": $new = "logout"; break;
        case "deluser": $new = "Delete user"; break;
        case "addnewuser": $new = "Add new user"; break;
        case "login": $new = "Login"; break;
        case "password": $new = "Password"; break;
        case "userwarning": $new = "Be sure to remember your password, you can't recover it !"; break;
        case "userdelok": $new = "Are you sure to delete this user ?"; break;
        default: $new = "<b>".$zin."</b> needs to be translated !";    break;

    }
    return $new;
}
?>
