<?
######################################################################
#Tradotto da by Fabio Mascio
#Translated by Fabio mascio
#http://www.italywebspa.com
#fmascio@cercaziende.it
#######################################################################
function translate($zin){

    switch ($zin) {
        
        case "Admin": $new = "Amministrazione";    break;
        case "cate": $new = "Mostra per Categorie"; break;
        case "day": $new = "Mostra per giorno"; break;
        case "week": $new = "Mostra per settimane"; break;
        case "cal": $new = "Questo Mese"; break;
        case "nocats": $new = "Nessuna Categoria"; break;
        case "addcat": $new = "Aggiungi Categoria"; break;
        case "cats": $new = "Categorie"; break;
        case "addevent": $new = "Aggiungi Evento"; break;
        case "outof": $new = "Eventi Storici"; break;
        case "eventitle": $new = "Titolo"; break;
        case "description": $new = "Descrizione"; break;
        case "choosecat": $new = "Scegli la Categoria"; break;
        case "selectyear": $new = "Anno"; break;
        case "selectmonth": $new = "Mese"; break;
        case "selectday": $new = "Giorno"; break;
        case "bdate": $new = "Data"; break;
        case "notitle": $new = "Devi immettere un titolo !"; break;
        case "nodescription": $new = "Devi immettere una descrizione"; break;
        case "noday": $new = "Devi scegliere un giorno !"; break;
        case "nomonth": $new = "Devi scegliere un Mese !"; break;
        case "noyear": $new = "Devi scegliere un Anno !"; break;
        case "nocat": $new = "Devi scegliere una categoria !"; break;
        case "back": $new = "Indietro"; break;
        case "nononapproved": $new = "Non ci sono eventi da approvare"; break;
        case "nonapproved": $new = "Eventi da approvare : "; break;
        case "cat": $new = "Categoria"; break;
        case "view": $new = "Mostra evento"; break;
        case "edit": $new = "Edita Evento"; break;
        case "approve": $new = "Approva Evento"; break;
        case "moreinfo": $new = "Maggiori dettagli"; break;
        case "editcat": $new = "Edita Categoria"; break;
        case "delcat": $new = "Elimina Categoria"; break;
        case "edit": $new = "Edita"; break;
        case "del": $new = "Elimina"; break;
        case "name": $new = "Nome"; break;
        case "update": $new = "Aggiorna"; break;
        case "reallydelcat": $new = "Sei sicuro di voler rimuovere questa categoria ? Tutti gli eventi associati a questa categoria verranno eliminati definitivamente !"; break;
        case "noback": $new = "Oops, no, Torna Indietro !"; break;
        case "surecat": $new = "Si, elimina adesso !"; break;
        case "noevents": $new = "Nessun Evento"; break;
        case "numbevents": $new = "Evento nella Categoria "; break;
        case "upevent": $new = "Aggiorna evento"; break;
        case "delev": $new = "Elimina evento"; break;
        case "nooutofdate": $new = "Nessun evento senza data."; break;
        case "delalloodev": $new = "Eliminare tutti gli eventi senza data"; break;
        case "delevok": $new = "Si, elimina evento !"; break;
        case "delalloodevok": $new = "Elimina tutti !"; break;
        case "prevm": $new = "Mese precedente"; break;
        case "nextm": $new = "Prossimo Mese"; break;
        case "todaysdate": $new = "Oggi"; break;
        case "today": $new = "Eventi di Oggi"; break;
        case "readmore": $new = "Continua"; break;
        case "nextday": $new = "Giorno successivo"; break;
        case "prevday": $new = "Giorno Precedente"; break;
        case "askedday": $new = "Giorno chiesto"; break;
        case "nextweek": $new = "Prossima settimana"; break;
        case "prevweek": $new = "Settimana precedente"; break;
        case "weeknr": $new = "Settimana numero"; break;
        case "eventsthisweek": $new = "Evento dal "; break;
        case "till": $new = "al"; break;
        case "thankyou": $new = "Grazie per aver inviato il tuo evento, sarà online entro breve tempo"; break;
        case "op": $new = "on"; break;
	# here start the new not yet translated language vars
        case "disabled": $new = "Questa sezione è disabilitata"; break;
	case "searchbutton": $new = "cerca"; break;
	case "searchtitle": $new = "Cerca"; break;
	case "onedate": $new = "Una data"; break;
        case "moredates": $new = "Più date"; break;
	case "moredatesexplain": $new = "Più date: 'dd-mm-yyyy;dd-mm-yyyy' se il giorno è 1, immetti 01, la stessa cosa per il mese! senza-';' !"; break;
	case "email": $new = "Email"; break;
	case "results": $new = "Risultati"; break;
	case "noresults": $new = "Nessun risultato"; break;
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
        default: $new = "<b>".$zin."</b> deve essere tradotto !";    break;
        
    }
    return $new;
}
?>
