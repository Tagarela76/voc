<?
function translate($zin){

    switch ($zin) {
        
        case "Admin": $new = "������";    break;
        case "cate": $new = "ī�װ��� ����"; break;
        case "day": $new = "�Ϻ��� ����"; break;
        case "week": $new = "�ֺ��� ����"; break;
        case "cal": $new = "�̹��� �޷�"; break;
        case "nocats": $new = "���� ī�װ��� �����."; break;
        case "addcat": $new = "ī�װ� �߰�"; break;
        case "cats": $new = "ī�װ�"; break;
        case "addevent": $new = "���� �߰�"; break;
        case "outof": $new = "������ ���� ��ϵ�"; break;
        case "eventitle": $new = "���� ����"; break;
        case "description": $new = "�̺�Ʈ ����"; break;
        case "choosecat": $new = "ī�װ� ����"; break;
        case "selectyear": $new = "�⼱��"; break;
        case "selectmonth": $new = "�޼���"; break;
        case "selectday": $new = "��¥����"; break;
        case "bdate": $new = "��¥"; break;
        case "notitle": $new = "������ �Է��Ͽ� �ּ���."; break;
        case "nodescription": $new = "������ �Է��Ͽ� �ּ���."; break;
        case "noday": $new = "���� ����ּ���."; break;
        case "nomonth": $new = "���� ����ּ���."; break;
        case "nocat": $new = "ī�װ��� ����ּ���."; break;
        case "noyear": $new = "���� ����ּ���"; break;
        case "back": $new = "���ư���"; break;
        case "nononapproved": $new = "����� ������ �����ϴ�."; break;
        case "op": $new = " op "; break;
        case "cat": $new = "ī�װ�"; break;
        case "view": $new = "����"; break;
        case "edit": $new = "�����ϱ�"; break;
        case "approve": $new = "����ϱ�"; break;
        case "nonapproved": $new = "��ϵ��� �ʾ��� : "; break;
        case "moreinfo": $new = "�� ���� ������..."; break;
        case "editcat": $new = "ī�װ� ����"; break;
        case "delcat": $new = "ī�װ� ����"; break;
        case "edit": $new = "�����ϱ�"; break;
        case "del": $new = "�����"; break;
        case "name": $new = "�̸�"; break;
        case "update": $new = "����"; break;
        case "reallydelcat": $new = "������ ī�װ��� ����ðڽ��ϱ�? �׷��� �� ī�װ��� ��� ��������� ���� �����˴ϴ�!!"; break;
        case "noback": $new = "�ڷ� ���� �����ϴ�."; break;
        case "surecat": $new = "��, ���� �����մϴ�."; break;
        case "noevents": $new = "������ �����ϴ�."; break;
        case "numbevents": $new = "ī�װ����� ����"; break;
        case "upevent": $new = "���� ���"; break;
        case "delev": $new = "���� ����"; break;
        case "nooutofdate": $new = "���� ������ �����ϴ�."; break;
        case "delalloodev": $new = "���� ������ ��� ����ϴ�."; break;
        case "delevok": $new = "��, ������ ����ϴ�."; break;
        case "delalloodevok": $new = "��, ������ ��� ����ϴ�."; break;
        case "prevm": $new = "������"; break;
        case "nextm": $new = "������"; break;
        case "todaysdate": $new = "����"; break;
        case "today": $new = "������"; break;
        case "readmore": $new = "�� �� �ڼ���"; break;
        case "nextday": $new = "������"; break;
        case "prevday": $new = "����"; break;
        case "askedday": $new = "�˻��ȳ�"; break;
        case "nextweek": $new = "������"; break;
        case "prevweek": $new = "������"; break;
        case "weeknr": $new = "��° ��"; break;
        case "eventsthisweek": $new = "���� "; break;
        case "till": $new = "����"; break;
        case "thankyou": $new = "�����մϴ�."; break;
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
        case "userman": $new = "User management"; break;
        case "users": $new = "users"; break;
        case "wronglogin": $new = "Something wrong with login or password"; break;
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
