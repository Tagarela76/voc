<?
function translate($zin){

    switch ($zin) {
        
        case "Admin": $new = "Administration";    break;
        case "cate": $new = "Kategorie Auswahl"; break;
        case "day": $new = "Tagesansicht"; break;
        case "week": $new = "Wochenansicht"; break;
        case "cal": $new = "Diesen Monat"; break;
        case "nocats": $new = "Zur Zeit keine Kategorien vorhanden"; break;
        case "addcat": $new = "Kategori(en) hinzufügen"; break;
        case "cats": $new = "Kategorien"; break;
        case "addevent": $new = "Termin hinzufügen"; break;
        case "outof": $new = "Vergangene Termine"; break;
        case "eventitle": $new = "Terminart"; break;
        case "description": $new = "Terminbeschreibung"; break;
        case "choosecat": $new = "Wähle Kategorie"; break;
        case "selectyear": $new = "Jahr"; break;
        case "selectmonth": $new = "Monat"; break;
        case "selectday": $new = "Tag"; break;
        case "bdate": $new = "Datum"; break;
        case "notitle": $new = "Sie müssen eine Terminart angeben!"; break;
        case "nodescription": $new = "Sie müssen eine Terminbeschreibung eingeben!"; break;
        case "noday": $new = "Sie müssen einen Tag wählen!"; break;
        case "nomonth": $new = "Sie müssen einen Monat wählen!"; break;
        case "noyear": $new = "Sie müssen ein Jahr wählen!"; break;
        case "nocat": $new = "Sie müssen eine Kategorie wählen!"; break;
        case "back": $new = "Zurück"; break;
        case "nononapproved": $new = "Es gibt zur Zeit keine Termine die zu bestätigen sind!"; break;
        case "nonapproved": $new = "Termine die eine Bestätigung erwarten : "; break;
        case "cat": $new = "Kategorie"; break;
        case "view": $new = "Terminansicht"; break;
        case "edit": $new = "Termin editieren"; break;
        case "approve": $new = "Bestätigen"; break;
        case "moreinfo": $new = "Weitere Infos"; break;
        case "editcat": $new = "Kategorie editieren"; break;
        case "delcat": $new = "Kategorie entfernen"; break;
        case "edit": $new = "Editieren"; break;
        case "del": $new = "Löschen"; break;
        case "name": $new = "Name"; break;
        case "update": $new = "Update"; break;
        case "reallydelcat": $new = "Sind Sie sicher, dass Sie diese Kategorie löschen wollen? Alle enthaltenen Termine zu dieser Kategorie werden für immer gelöscht!"; break;
        case "noback": $new = "Oops, nein, zurück!"; break;
        case "surecat": $new = "Ja, alle löschen!"; break;
        case "noevents": $new = "Keine Termine"; break;
        case "numbevents": $new = "Termine in der Kategorie "; break;
        case "upevent": $new = "Termin aktualisieren"; break;
        case "delev": $new = "Termin löschen"; break;
        case "nooutofdate": $new = "Keine vergangenen Termine."; break;
        case "delalloodev": $new = "Lösche alle vergangenen Termine"; break;
        case "delevok": $new = "Ja, Termin löschen!"; break;
        case "delalloodevok": $new = "Alle löschen!"; break;
        case "prevm": $new = "Vorheriger Monat"; break;
        case "nextm": $new = "Nächster Monat"; break;
        case "todaysdate": $new = "Heute"; break;
        case "today": $new = "Termine heute"; break;
        case "readmore": $new = "Mehr lesen"; break;
        case "nextday": $new = "Nächster Tag"; break;
        case "prevday": $new = "Vorheriger Tag"; break;
        case "askedday": $new = "Tag"; break;
        case "nextweek": $new = "Nächste Woche"; break;
        case "prevweek": $new = "Vorherige Woche"; break;
        case "weeknr": $new = "Woche Nr."; break;
        case "eventsthisweek": $new = "Termine vom "; break;
        case "till": $new = " bis "; break;
        case "thankyou": $new = "Danke für Ihren Terminvorschlag, nach bestandener Prüfung wird er bald erscheinen!"; break;
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
