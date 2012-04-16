<?
function translate($zin){

    switch ($zin) {
        
        case "Admin": $new = "Administratie";    break;
        case "cate": $new = "Surf per categorie"; break;
        case "day": $new = "Surf per dag"; break;
        case "week": $new = "Surf per week"; break;
        case "cal": $new = "Maandelijkse calender"; break;
        case "nocats": $new = "Nog geen categorien"; break;
        case "addcat": $new = "Voeg categorie toe"; break;
        case "cats": $new = "Categorien"; break;
        case "addevent": $new = "Voeg event toe"; break;
        case "outof": $new = "'Out-of-date' events"; break;
        case "eventitle": $new = "Titel event"; break;
        case "description": $new = "Omschrijving event"; break;
        case "choosecat": $new = "Kies categorie"; break;
        case "selectyear": $new = "Jaar"; break;
        case "selectmonth": $new = "Maand"; break;
        case "selectday": $new = "Dag"; break;
        case "bdate": $new = "Datum"; break;
        case "notitle": $new = "Je moet een titel ingeven !"; break;
        case "nodescription": $new = "Je moet een omschrijving ingeven !"; break;
        case "noday": $new = "Je moet een dag kiezen !"; break;
        case "nomonth": $new = "Je moet een maand kiezen !"; break;
        case "nocat": $new = "Je moet een categorie kiezen !"; break;
        case "noyear": $new = "Je moet een jaar kiezen !"; break;
        case "back": $new = "Terug"; break;
        case "nononapproved": $new = "Er zijn geen niet-goedgekeurde events momenteel"; break;
        case "op": $new = " op "; break;
        case "cat": $new = "Categorie"; break;
        case "view": $new = "Bekijk event"; break;
        case "edit": $new = "Editeer event"; break;
        case "approve": $new = "Keur goed"; break;
        case "nonapproved": $new = "Niet goedgekeurde events : "; break;
        case "moreinfo": $new = "Meer informatie"; break;
        case "editcat": $new = "Editeer categorie"; break;
        case "delcat": $new = "Verwijder categorie"; break;
        case "edit": $new = "Editeer"; break;
        case "del": $new = "Verwijder"; break;
        case "name": $new = "Naam"; break;
        case "update": $new = "Pas aan"; break;
        case "reallydelcat": $new = "Ben je zeker dat je deze categorie wilt verwijderen ? Immers, ALLE events die eronder vallen worden ook verwijderd !"; break;
        case "noback": $new = "Oeps, nee, ga terug !"; break;
        case "surecat": $new = "Ja, heel zeker, verwijder maar !"; break;
        case "noevents": $new = "Geen evenementen"; break;
        case "numbevents": $new = "Evenementen in categorie "; break;
        case "upevent": $new = "Pas event aan"; break;
        case "delev": $new = "Verwijder event"; break;
        case "nooutofdate": $new = "Geen out-of-date events."; break;
        case "delalloodev": $new = "Verwijder alle out-of-date events"; break;
        case "delevok": $new = "Ja, verwijder event !"; break;
        case "delalloodevok": $new = "Ja, verwijder ze allemaal"; break;
        case "prevm": $new = "Vorige maand"; break;
        case "nextm": $new = "Volgende maand"; break;
        case "todaysdate": $new = "Vandaag"; break;
        case "today": $new = "Events vandaag"; break;
        case "readmore": $new = "Lees meer"; break;
        case "nextday": $new = "Volgende dag"; break;
        case "prevday": $new = "Vorige dag"; break;
        case "askedday": $new = "Gevraagde dag"; break;
        case "nextweek": $new = "Volgende week"; break;
        case "prevweek": $new = "Vorige week"; break;
        case "weeknr": $new = "weeknummer"; break;
        case "eventsthisweek": $new = "Evenementen van "; break;
        case "till": $new = " tot "; break;
        case "thankyou": $new = "Bedankt voor je ingeving, we zullen het zo snel mogelijk controleren !"; break;
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
        case "wronglogin": $new = "Er ging iets fout met login of paswoord!"; break;
        case "users" : $new = "users"; break;
        case "userman": $new = "User management"; break;
        case "logout": $new = "logout"; break;
        case "deluser": $new = "Delete user"; break;
        case "addnewuser": $new = "Add new user"; break;
        case "login": $new = "Login"; break;
        case "password": $new = "Password"; break;
        case "userwarning": $new = "Opgelet, onthoud je paswoord, want je kan het niet recupereren !"; break;
        case "userdelok": $new = "Ben je zeker om deze gebruiker te verwijderen ?"; break;
        default: $new = "<b>".$zin."</b> needs to be translated !";    break;

    }
    return $new;
}
?>
