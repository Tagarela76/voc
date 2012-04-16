<?
function translate($zin){

    switch ($zin) {

        case "Admin": $new = "Administraciónon";    break;
        case "cate": $new = "Ver por categorias"; break;
        case "day": $new = "Ver por dias"; break;
        case "week": $new = "Ver por semanas"; break;
        case "cal": $new = "Calendario por meses"; break;
        case "nocats": $new = "No existen categorias"; break;
        case "addcat": $new = "Agregar  categoria"; break;
        case "cats": $new = "Categorias"; break;
        case "addevent": $new = "Agregar Evento"; break;
        case "outof": $new = "Historial items"; break;
        case "eventitle": $new = "Título del Evento"; break;
        case "description": $new = "Descripción del Evento"; break;
        case "choosecat": $new = "Choose category"; break;
        case "selectyear": $new = "Año"; break;
        case "selectmonth": $new = "Mes"; break;
        case "selectday": $new = "Día"; break;
        case "bdate": $new = "Fecha"; break;
        case "notitle": $new = "Debe agregar un titilo al evento !"; break;
        case "nodescription": $new = "Debe agregar una descripcion al evento "; break;
        case "noday": $new = "Debe selecciona un día !"; break;
        case "nomonth": $new = "Debe selecciona un Mes !"; break;
        case "noyear": $new = "Debe selecciona un año !"; break;
        case "nocat": $new = "Debe selecciona una categoría!"; break;
        case "back": $new = "Regresar"; break;
        case "nononapproved": $new = "No hay eventos para aprobar en este momento..."; break;
        case "nonapproved": $new = "Evento requiere aprobación: "; break;
        case "cat": $new = "Categoría"; break;
        case "view": $new = "Ver eventos"; break;
        case "edit": $new = "Editar Evento"; break;
        case "approve": $new = "Aprobar"; break;
        case "moreinfo": $new = "Mayor información"; break;
        case "editcat": $new = "Editar categoria"; break;
        case "delcat": $new = "Borrar Categorias"; break;
        case "edit": $new = "Editar"; break;
        case "del": $new = "Borrar"; break;
        case "name": $new = "Nombre"; break;
        case "update": $new = "Actualizar"; break;
        case "reallydelcat": $new = "Está seguro de borrar ésta categoria ? Todos los eventos asociados a esta categoria será eliminados permanentemente!"; break;
        case "noback": $new = "Oops, no, regrsar !"; break;
        case "surecat": $new = "Si, borrelo ahora !"; break;
        case "noevents": $new = "No hay eventos"; break;
        case "numbevents": $new = "Evento por categorias "; break;
        case "upevent": $new = "Actialuzar evento"; break;
        case "delev": $new = "Borrar Evento"; break;
        case "nooutofdate": $new = "No out-of-date events."; break;
        case "delalloodev": $new = "Delete all out-of-date events"; break;
        case "delevok": $new = "Si, borre todos los Eventos !"; break;
        case "delalloodevok": $new = "Borrarlos Todos !"; break;
        case "prevm": $new = "Mes Anterior"; break;
        case "nextm": $new = "Siguiente Mes"; break;
        case "todaysdate": $new = "Hoy"; break;
        case "today": $new = "Eventos para hoy"; break;
        case "readmore": $new = "Leer mas"; break;
        case "nextday": $new = "Siguiente día"; break;
        case "prevday": $new = "Día anterior"; break;
        case "askedday": $new = "Preguntar día"; break;
        case "nextweek": $new = "Siguiente semana"; break;
        case "prevweek": $new = "Semana Anterior"; break;
        case "weeknr": $new = "Semana número "; break;
        case "eventsthisweek": $new = "Eventos del "; break;
        case "till": $new = "al"; break;
        case "thankyou": $new = "Thank you for entering an event, it will apear shortly"; break;
        case "op": $new = "on"; break;
        case "disabled": $new = "This section has been disabled"; break;
	case "searchbutton": $new = "buscar"; break;
	case "searchtitle": $new = "Buscar"; break;
	case "onedate": $new = "Una Fecha"; break;
        case "moredates": $new = "Mas fechas"; break;
	case "moredatesexplain": $new = "Para mas días separe con (;): 'dd-mm-aaaa;dd-mm-aaaa' si día es uno, escriba 01, igualmente hágalo con los meses!!"; break;
	case "email": $new = "Email"; break;
	case "results": $new = "resultados"; break;
	case "noresults": $new = "No se obtuvieron Resultados"; break;
        # here start the not-yet translated words
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
        default: $new = "<b>".$zin."</b> Necesita ser traducido !";    break;

    }
    return $new;
}
?>
