<?
function translate($zin){

    switch ($zin) {

        case "Admin": $new = "������";    break;
        case "cate": $new = "����� ��� �������"; break;   
        case "day": $new = "����� ����"; break;
        case "week": $new = "����� ������"; break;
        case "cal": $new = "������� ������"; break;
        case "nocats": $new = "������ ������� "; break;
        case "addcat": $new = "��� ������"; break;
        case "cats": $new = "���������"; break;
        case "addevent": $new = "��� ����"; break; 
        case "outof": $new = "'����� �����"; break;
        case "eventitle": $new = "��� �����"; break;
        case "description": $new = "��� �����"; break;
        case "choosecat": $new = "���� ������"; break;
        case "selectyear": $new = "�����"; break;    
        case "selectmonth": $new = "�����"; break;
        case "selectday": $new = "�����"; break;
        case "bdate": $new = "�������"; break;
        case "notitle": $new = "��� ����� ��� �����"; break;
        case "nodescription": $new = "��� ����� ��� �����"; break;
        case "noday": $new = "��� �� ����� ����"; break;
        case "nomonth": $new = "��� �� ����� ����"; break;
        case "noyear": $new = "��� �� ����� ����"; break;
        case "nocat": $new = "��� �� ����� ������"; break;
        case "back": $new = "����"; break;
        case "nononapproved": $new = "������ ����� �� ��� ��������"; break;
        case "nonapproved": $new = "����� �� ��� ��������: "; break;
        case "cat": $new = "�������"; break;
        case "view": $new = "���� �����"; break;
        case "edit": $new = "��� �����"; break;
        case "approve": $new = "������"; break;
        case "moreinfo": $new = "������"; break;   
        case "editcat": $new = "��� �������"; break;
        case "delcat": $new = "��� �������"; break;
        case "edit": $new = "�����"; break;
        case "del": $new = "���"; break;
        case "name": $new = "�����"; break;
        case "update": $new = "�����"; break;
        case "reallydelcat": $new = "�� ��� ����� �� ��� ������ݿ �� ������� �������� ���� ������� ���� ������� �����"; break;
        case "noback": $new = "��� ��, ������ ������"; break;
        case "surecat": $new = "��� ���� ����"; break;
        case "noevents": $new = "������ �����"; break;
        case "numbevents": $new = "������� �� ������� "; break;
        case "upevent": $new = "����� �����"; break;
        case "delev": $new = "��� �����"; break;
        case "nooutofdate": $new = "������ ����� �����"; break;
        case "delalloodev": $new = "��� ���� ������� �������"; break;
        case "delevok": $new = "��� ���� �����"; break;
        case "delalloodevok": $new = "���� ������"; break;
        case "prevm": $new = "����� ������"; break;
        case "nextm": $new = "����� ������"; break;
        case "todaysdate": $new = "�����"; break;
        case "today": $new = "����� �����"; break;
        case "readmore": $new = "������"; break;
        case "nextday": $new = "����� ������"; break;
        case "prevday": $new = "����� ������"; break;
        case "askedday": $new = "�������"; break;
        case "nextweek": $new = "������� ������"; break;
        case "prevweek": $new = "������� ������"; break;
        case "weeknr": $new = "��� �������"; break;
        case "eventsthisweek": $new = " �� "; break;
        case "till": $new = "���� "; break;
        case "thankyou": $new = "���� ������� ���ǡ ���� ������ �� ������� �����"; break;
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

