<?
function translate($zin){

    switch ($zin) {
        
        case "Admin": $new = "Administration";    break;
        case "cate": $new = "Kategorie Auswahl"; break;
        case "day": $new = "Tagesansicht"; break;
        case "week": $new = "Wochenansicht"; break;
        case "cal": $new = "Diesen Monat"; break;
        case "nocats": $new = "Zur Zeit keine Kategorien vorhanden"; break;
        case "addcat": $new = "Kategori(en) hinzuf�gen"; break;
        case "cats": $new = "Kategorien"; break;
        case "addevent": $new = "Termin hinzuf�gen"; break;
        case "outof": $new = "Vergangene Termine"; break;
        case "eventitle": $new = "Terminart"; break;
        case "description": $new = "Terminbeschreibung"; break;
        case "choosecat": $new = "W�hle Kategorie"; break;
        case "selectyear": $new = "Jahr"; break;
        case "selectmonth": $new = "Monat"; break;
        case "selectday": $new = "Tag"; break;
        case "bdate": $new = "Datum"; break;
        case "notitle": $new = "Sie m�ssen eine Terminart angeben!"; break;
        case "nodescription": $new = "Sie m�ssen eine Terminbeschreibung eingeben!"; break;
        case "noday": $new = "Sie m�ssen einen Tag w�hlen!"; break;
        case "nomonth": $new = "Sie m�ssen einen Monat w�hlen!"; break;
        case "noyear": $new = "Sie m�ssen ein Jahr w�hlen!"; break;
        case "nocat": $new = "Sie m�ssen eine Kategorie w�hlen!"; break;
        case "back": $new = "Zur�ck"; break;
        case "nononapproved": $new = "Es gibt zur Zeit keine Termine die zu best�tigen sind!"; break;
        case "nonapproved": $new = "Termine die eine Best�tigung erwarten : "; break;
        case "cat": $new = "Kategorie"; break;
        case "view": $new = "Terminansicht"; break;
        case "edit": $new = "Termin editieren"; break;
        case "approve": $new = "Best�tigen"; break;
        case "moreinfo": $new = "Weitere Infos"; break;
        case "editcat": $new = "Kategorie editieren"; break;
        case "delcat": $new = "Kategorie entfernen"; break;
        case "edit": $new = "Editieren"; break;
        case "del": $new = "L�schen"; break;
        case "name": $new = "Name"; break;
        case "update": $new = "Update"; break;
        case "reallydelcat": $new = "Sind Sie sicher, dass Sie diese Kategorie l�schen wollen? Alle enthaltenen Termine zu dieser Kategorie werden f�r immer gel�scht!"; break;
        case "noback": $new = "Oops, nein, zur�ck!"; break;
        case "surecat": $new = "Ja, alle l�schen!"; break;
        case "noevents": $new = "Keine Termine"; break;
        case "numbevents": $new = "Termine in der Kategorie "; break;
        case "upevent": $new = "Termin aktualisieren"; break;
        case "delev": $new = "Termin l�schen"; break;
        case "nooutofdate": $new = "Keine vergangenen Termine."; break;
        case "delalloodev": $new = "L�sche alle vergangenen Termine"; break;
        case "delevok": $new = "Ja, Termin l�schen!"; break;
        case "delalloodevok": $new = "Alle l�schen!"; break;
        case "prevm": $new = "Vorheriger Monat"; break;
        case "nextm": $new = "N�chster Monat"; break;
        case "todaysdate": $new = "Heute"; break;
        case "today": $new = "Termine heute"; break;
        case "readmore": $new = "Mehr lesen"; break;
        case "nextday": $new = "N�chster Tag"; break;
        case "prevday": $new = "Vorheriger Tag"; break;
        case "askedday": $new = "Tag"; break;
        case "nextweek": $new = "N�chste Woche"; break;
        case "prevweek": $new = "Vorherige Woche"; break;
        case "weeknr": $new = "Woche Nr."; break;
        case "eventsthisweek": $new = "Termine vom "; break;
        case "till": $new = " bis "; break;
        case "thankyou": $new = "Danke f�r Ihren Terminvorschlag, nach bestandener Pr�fung wird er bald erscheinen!"; break;
        case "op": $new = "am"; break;
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
        case "wronglogin": $new = "Something wrong with login or password"; break;
        case "logout": $new = "Log out"; break;
        case "users" : $new = "users"; break;
        case "userman": $new = "User management"; break;
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
