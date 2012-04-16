<?
function translate($zin){

    switch ($zin) {

        case "Admin": $new = "Administration";    break;
        case "cate": $new = "Vu par categories"; break;
        case "day": $new = "Vu par jour"; break;
        case "week": $new = "Vu par semaine"; break;
        case "cal": $new = "Mois en cours "; break;
        case "nocats": $new = "Pas encore de categorie "; break;
        case "addcat": $new = "Ajouter une categorie"; break;
        case "cats": $new = "Categories"; break;
        case "addevent": $new = "Ajouter évènements"; break;
        case "outof": $new = " Evènements en dehors du mois en cours "; break;
        case "eventitle": $new = "Titre de l'évènement"; break;
        case "description": $new = "Description de l'évènement"; break;
        case "choosecat": $new = "Choisir une categorie"; break;
        case "selectyear": $new = "Année"; break;
        case "selectmonth": $new = "Mois"; break;
        case "selectday": $new = "Jour"; break;
        case "bdate": $new = "Date"; break;
        case "notitle": $new = "Veuillez donner un titre"; break;
        case "nodescription": $new = "Veuillez donner une description"; break;
        case "noday": $new = "Veuillez choisir un jour"; break;
        case "nomonth": $new = "Veuillez choisir un titre "; break;
        case "noyear": $new = "Veuillez choisir une année"; break;
        case "nocat": $new = "Veuillez choisir une categorie"; break;
        case "back": $new = "Retour"; break;
        case "nononapproved": $new = "Aucun évènement non approuvé pour l'instant"; break;
        case "op": $new = "  "; break;
        case "nonapproved": $new = "Evènements non approuvés : "; break;
        case "cat": $new = "Categories"; break;
        case "view": $new = "Voir évènement "; break;
        case "edit": $new = "Editer évènement "; break;
        case "approve": $new = "Approuver"; break;
        case "moreinfo": $new = "Plus d'info"; break;
        case "editcat": $new = "Voir les categories"; break;
        case "delcat": $new = "Suppression categorie"; break;
        case "edit": $new = "Editer"; break;
        case "del": $new = "Supprimer"; break;
        case "name": $new = "Nom"; break;
        case "update": $new = "Mise à jour "; break;
        case "reallydelcat": $new = "Etes-vous sûr de vouloir supprimer cette categorie ? "; break;
        case "noback": $new = "Oops, non, ... retour !"; break;
        case "surecat": $new = "Oui,suppression !"; break;
        case "noevents": $new = "Aucun évènements "; break;
        case "numbevents": $new = "Evènements dans la categorie : "; break;
        case "upevent": $new = "Mise à jour évènements "; break;
        case "delev": $new = "Suppression évènements "; break;
        case "nooutofdate": $new = "Aucun évenement en dehors du mois courant."; break;
        case "delalloodev": $new = "Supprimer tous les évènements "; break;
        case "delevok": $new = "Oui, suppresion de l'évènement "; break;
        case "delalloodevok": $new = "Supprimer les tous"; break;
        case "prevm": $new = "Mois précedent"; break;
        case "nextm": $new = "Mois suivant "; break;
        case "todaysdate": $new = "Aujourd'hui "; break;
        case "today": $new = "Evenements quotidien"; break;
        case "readmore": $new = "En savoir plus "; break;
        case "nextday": $new = "Jour suivant "; break;
        case "prevday": $new = "Jour précedent "; break;
        case "askedday": $new = "Jour recherché "; break;
        case "nextweek": $new = "Semaine suivante "; break;
        case "prevweek": $new = "Semaine précedente"; break;
        case "weeknr": $new = "Nombre de semaines "; break;
        case "eventsthisweek": $new = "Evènements de "; break;
        case "till": $new = "durant "; break;
        case "thankyou": $new = "Merci,nous traiterons votre évènement au plus vite "; break;
        case "": $new = "Ecrit par "; break;
	# here start the new not yet translated language vars
        case "disabled": $new = "This section has been disabled"; break;
	case "searchbutton": $new = "search"; break;
        case "searchtitle": $new = "Search"; break;
	case "onedate": $new = "One date"; break;
        case "moredates": $new = "More dates"; break;
	case "moredatesexplain": $new = "More dates: 'dd-mm-yyyy;dd-mm-yyyy' if day is one, type 01, same for month! without end-';' !"; break;
	case "email": $new = "email"; break;
        case "results": $new = "results"; break;
        case "logout": $new = "Log out"; break;
        case "users" : $new = "users"; break;
        case "userman": $new = "User management"; break;
        case "deluser": $new = "Delete user"; break;
        case "addnewuser": $new = "Add new user"; break;
        case "login": $new = "Login"; break;
        case "password": $new = "Password"; break;
        case "userwarning": $new = "Be sure to remember your password, you can't recover it !"; break;
        case "userdelok": $new = "Are you sure to delete this user ?"; break;
        case "wronglogin": $new = "Something wrong with login or password"; break;
        case "noresults": $new = "No results"; break;
        default: $new = "<b>".$zin."</b> doit etre traduit ";    break;

    }
    return $new;
}
?>
