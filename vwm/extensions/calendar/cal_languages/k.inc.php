<?
function translate($zin){

    switch ($zin) {
        
        case "Admin": $new = "관리자";    break;
        case "cate": $new = "카테고리로 열람"; break;
        case "day": $new = "일별로 열람"; break;
        case "week": $new = "주별로 열람"; break;
        case "cal": $new = "이번달 달력"; break;
        case "nocats": $new = "아직 카테고리가 없어요."; break;
        case "addcat": $new = "카테고리 추가"; break;
        case "cats": $new = "카테고리"; break;
        case "addevent": $new = "일정 추가"; break;
        case "outof": $new = "지나간 일정 목록들"; break;
        case "eventitle": $new = "일정 제목"; break;
        case "description": $new = "이벤트 본문"; break;
        case "choosecat": $new = "카테고리 선택"; break;
        case "selectyear": $new = "년선택"; break;
        case "selectmonth": $new = "달선택"; break;
        case "selectday": $new = "날짜선택"; break;
        case "bdate": $new = "날짜"; break;
        case "notitle": $new = "제목을 입력하여 주세요."; break;
        case "nodescription": $new = "본문을 입력하여 주세요."; break;
        case "noday": $new = "날을 골라주세요."; break;
        case "nomonth": $new = "달을 골라주세요."; break;
        case "nocat": $new = "카테고리를 골라주세요."; break;
        case "noyear": $new = "년을 골라주세요"; break;
        case "back": $new = "돌아가기"; break;
        case "nononapproved": $new = "등록할 일정이 없습니다."; break;
        case "op": $new = " op "; break;
        case "cat": $new = "카테고리"; break;
        case "view": $new = "보기"; break;
        case "edit": $new = "편집하기"; break;
        case "approve": $new = "등록하기"; break;
        case "nonapproved": $new = "등록되지 않았음 : "; break;
        case "moreinfo": $new = "더 많은 정보를..."; break;
        case "editcat": $new = "카테고리 편집"; break;
        case "delcat": $new = "카테고리 삭제"; break;
        case "edit": $new = "편집하기"; break;
        case "del": $new = "지우기"; break;
        case "name": $new = "이름"; break;
        case "update": $new = "갱신"; break;
        case "reallydelcat": $new = "정말로 카테고리를 지우시겠습니까? 그러면 이 카테고리의 모든 일정목록이 같이 삭제됩니다!!"; break;
        case "noback": $new = "뒤로 갈수 없습니다."; break;
        case "surecat": $new = "네, 지금 삭제합니다."; break;
        case "noevents": $new = "일정이 없습니다."; break;
        case "numbevents": $new = "카테고리내의 일정"; break;
        case "upevent": $new = "일정 등록"; break;
        case "delev": $new = "일정 삭제"; break;
        case "nooutofdate": $new = "지난 일정이 없습니다."; break;
        case "delalloodev": $new = "지난 일정을 모두 지웁니다."; break;
        case "delevok": $new = "네, 일정을 지웁니다."; break;
        case "delalloodevok": $new = "네, 일정을 모두 지웁니다."; break;
        case "prevm": $new = "이전달"; break;
        case "nextm": $new = "다음달"; break;
        case "todaysdate": $new = "오늘"; break;
        case "today": $new = "일정일"; break;
        case "readmore": $new = "좀 더 자세히"; break;
        case "nextday": $new = "다음날"; break;
        case "prevday": $new = "전날"; break;
        case "askedday": $new = "검색된날"; break;
        case "nextweek": $new = "다음주"; break;
        case "prevweek": $new = "이전주"; break;
        case "weeknr": $new = "번째 주"; break;
        case "eventsthisweek": $new = "부터 "; break;
        case "till": $new = "까지"; break;
        case "thankyou": $new = "감사합니다."; break;
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
