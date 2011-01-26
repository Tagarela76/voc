<?php

class Notify {
	private $smarty;
	private $db;
	
	function Notify($smarty,$db = null) {
		$this->smarty = $smarty;
		$this->db = $db;
	}
	
	/**
	 * Builds data for popup notify, that uses notify.js
	 *  array
	 *    'text' => string 'Cannot connect to database' (length=26)
	 *      'params' => 
	 *          array
	 *                'color' => string 'Black' (length=5)
	 *                'backgroundColor' => string 'Red' (length=3)
	 * @param mix_type $errorCode can be NULL than use $text for Message
	 * @param mix_type $additionalParams like array(key=>value),supported values: width, height, color, backgroundColor, fontSize.
	 * @param string $text if $errorCode == NULL than use $text for Message
	 */
	public function getPopUpNotifyMessage($errorCode,$additionalParams = NULL, $text = NULL){

			if($errorCode){
				$text = $this->getMessageByCode($errorCode);
			}
			
			$colors = 	$this->getErrorColors($errorCode);

			$notify = array("text" => $text,
							"params" => $colors);

			if($additionalParams){
				foreach($additionalParams as $key => $value){
					
						$notify['params'][$key] = $value;
				}
			}

			return $notify;
			
	}

	/**
	 * 
	 * Returns notify message from database table NotifyCode by code
	 * @param int $code
	 * @throws Exception "Wrong error code!" if code is wrong
	 */
	private function getMessageByCode(int $code){
	
		if(is_numeric($code)){
			$query = "select message from " . TB_NOTIFY_CODE . " where code = $code limit 1";
			$this->db->query($query);
			$message = $this->db->fetch_array(0);
			return $message['message'];
		}else{
			throw new Exception("Wrong error code!");
		}
	}
	
	/**
	 * 
	 * Get colors combination by code. 0-400 are notify colors (green background, white text color), 400 and more - error notifies (red background and black text)
	 * @param int $error ErrorCode
	 * @return Array("color" => "ColorName", "backgroundColor" => "ColorName"); 
	 */
	private function getErrorColors(int $error){

			if($error > 0 and $error < 400){
					return Array("color" => "White", "backgroundColor" => "Green");
			}elseif($error >= 400)
			{
					return Array("color" => "Black", "backgroundColor" => "Red");
			}
		
	}
	
	function warnDelete($categoryType, $categoryName = "", $linked = false, $count = 0, $info = null) {
		if ($categoryName=="") {			
			switch ($categoryType) {
				case "company":
					$categories="companies";
					break;
					
				case "facility":
					$categories="facilities";	
					break;
					
				case "department":
					$categories="departments";
					break;
					
				case "inventory":
					$categories="inventories";
					break;
					
				case "equipment":
					$categories="equipment";
					break;
					
				case "product":
					$categories="products";
					break;
					
				case "usage":
					if ($count == 1) {
						$categories = "mix";
					} else {
						$categories="mixes";
					}
					break;
				case "carbonfootprint":
					if ($count == 1) {
						$categories = "direct carbon emission";
					} else {
						$categories = "direct carbon emissions";
					}
					break;
				case "logbook":
					$categories = "logbook records";
					break;
				
				case "wastestorage":
					if ($info['error'] == 'date') {
						$this->smarty->assign("message","You are about to ".$info['method']." storages <b>" .$categoryName. "</b> on <b>".$info['date']." 0:00 AM</b>. You can not ".$info['method']." storages for future!");
						$this->smarty->assign("color", "orange");
						return;
					} 
					switch ($info['method']) {
						case 'delete':
							$this->smarty->assign("message","You are about to delete selected storages on ".$info['date']." 0:00 AM. Are you sure?");
							$this->smarty->assign("color", "orange");
							return;
							break;
						case 'restore':
							$this->smarty->assign("message","You are about to restore selected storages. Are you sure?");
							$this->smarty->assign("color", "orange");
							return;
							break;
						case 'empty':
							$this->smarty->assign("message","You are about to empty selected storages on ".$info['date']." 0:00 AM. Are you sure?");
							$this->smarty->assign("color", "orange");
							return;
							break;
					}
					break;
					
				default: ;
			}
			if ($linked){
				$this->smarty->assign("message", "Some of ".$categories." are linked with equipment.".
						"<br> Are you sure you want to delete linked equipment too?");
				$this->smarty->assign("color", "orange");
			} else {			
				$this->smarty->assign("message", "You are about to delete selected " .$categories. ". Are you sure?");
				$this->smarty->assign("color", "orange");
			}			
		} else {			
			switch ($categoryType) {
				case "company":
					$category="company";
					break;
					
				case "facility":
					$category="facility";
					break;
					
				case "department":
					$category="department";
					break;
					
				case "inventory":
					$category="inventory";
					break;
					
				case "equipment":
					$category="equipment";
					break;
					
				case "product":
					$category="product";
					break;
					
				case "usage":
					$category="mix";
					break;
					
				case "logbook":
					$category="logbook record";
					break;
				
				case "wastestorage":
					if ($info['error'] == 'date') {
						$this->smarty->assign("message","You are about to ".$info['method']." storage <b>" .$categoryName. "</b> on <b>".$info['date']." 0:00 AM</b>. You can not ".$info['method']." storage for future!");
						$this->smarty->assign("color", "orange");
						return;
					} 
					switch ($info['method']) {
						case 'delete':
							$this->smarty->assign("message","You are about to delete storage <b>" .$categoryName. "</b> on <b>".$info['date']." 0:00 AM</b>. Are you sure?");
							$this->smarty->assign("color", "orange");
							return;
							break;
						case 'restore':
							$this->smarty->assign("message","You are about to restore storage <b>" .$categoryName. "</b>. Are you sure?");
							$this->smarty->assign("color", "orange");
							return;
							break;
						case 'empty':
							$this->smarty->assign("message","You are about to empty storage <b>" .$categoryName. "</b> on <b>".$info['date']." 0:00 AM</b>. Are you sure?");
							$this->smarty->assign("color", "orange");
							return;
							break;
					}
					break;
				
				case "MSDS Sheet":
					$this->smarty->assign("message", "You are about to unlink " .$category. " <b>" .$categoryName. "</b>. Are you sure?");
					$this->smarty->assign("color", "orange");
					return;
					
				default: ;
			}
			if ($linked){
				$this->smarty->assign("message", "Attention! ".$category. " <b>" .$categoryName. "</b> is linked with equipment." .
						"<br> Are you sure you want to delete this equipment too?");
				$this->smarty->assign("color", "orange");
			} else {
				$this->smarty->assign("message", "You are about to delete " .$category. " <b>" .$categoryName. "</b>. Are you sure?");
				$this->smarty->assign("color", "orange");
			}
		}
	}
    
    
    function successDeleted($categoryType, $categoryNames) {
	    if (count($categoryNames)!=1) {
		    switch ($categoryType) {
			    case "company":
				    $message="Companies ";
				    break;
				    
			    case "facility":
				    $message="Facilities ";
				    break;
				    
			    case "department":
				    $message="Departments ";
				    break;
				    
			    case "inventory":
				    $message="Inventories ";
				    break;
				    
			    case "equipment":
				    $message="Equipment ";
				    break;
				    
			    case "product":
				    $message="Products ";
				    break;
				    
			    case "usages":
				    $message="Mixes ";
				    break;
				    
			    default: ;
		    }
		    $word="were";
	    }  else {	    	
		    switch ($categoryType) {
			    case "company":
				    $message="Company ";
				    break;
				    
			    case "facility":
				    $message="Facility ";
				    break;
				    
			    case "department":
				    $message="Department ";
				    break;
				    
			    case "inventory":
				    $message="Inventory ";
				    break;
				    
			    case "equipment":
				    $message="Equipment ";
				    break;
				    
			    case "product":
				    $message="Product ";
				    break;
				    
			    case "usage":
				    $message="Mix ";
				    break;
				    
				case "MSDS Sheet":				
				    $this->smarty->assign("message", "MSDS Sheet <b>".$categoryNames[0]."</b> was successfully unlinked");
					$this->smarty->assign("color", "green");					
					return;
			    default: ;
		    }
		    $word="was";
	    }
	    
	    if (count($categoryNames)>5) {
	    	$itemsCount=6;
	    } else {
	    	$itemsCount=count($categoryNames);
	    }
	    
	    for ($i=0;$i<$itemsCount-1;$i++){
    		$message.="<b>".$categoryNames[$i]."</b>, ";
    	}
    	
    	if (count($categoryNames)>5) {
	    	$message.="<b>...</b> ".$word." successfully deleted";
    	} else {
    		$message.="<b>".$categoryNames[count($categoryNames)-1]."</b> ".$word." successfully deleted";
    	}
    	
    	$this->smarty->assign("message", $message);
		$this->smarty->assign("color", "green");
	    
    }
    
    function successEdited($categoryType, $categoryName) {
	    
	    switch ($categoryType) {
		    case "company":
			    $category="Company";
			    break;
			    
		    case "facility":
			    $category="Facility";
			    break;
			    
		    case "department":
			    $category="Department";
			    break;
			    
		    case "product":
			    $category="Product";
			    break;
			    
		    case "equipment":
			    $category="Equipment";
			    break;
			    
		    case "inventory":
			    $category="Inventory";
			    break;
			    
		    case "usage":
			    $category="Mix";
			    break;
	    }
	    $this->smarty->assign("message", $category. " <b>" .$categoryName. "</b> was successfully edited");
	    $this->smarty->assign("color", "green");
    }
    
    function successAdded($categoryType, $categoryName) {
	    switch ($categoryType) {
		    case "company":
			    $category="Company";
			    break;
			    
		    case "facility":
			    $category="Facility";
			    break;
			    
		    case "department":
			    $category="Department";
			    break;
			    
		    case "product":
			    $category="Product";
			    break;
			    
		    case "inventory":
			    $category="Inventory";
			    break;
			    
		    case "equipment":
			    $category="Equipment";
			    break;
			    
		    case "usage":
			    $category="Mix";
			    break;
			    
		    default: ;
	    }
	    
	    $this->smarty->assign("message", $category. " <b>" .$categoryName. "</b> was successfully added");
	    $this->smarty->assign("color", "green");
    }
    
    
    function formErrors(){
    	$this->smarty->assign("message", "There are errors in the form<br>Correct them please!");
    	$this->smarty->assign("color", "orange");
    }
    
    function loginSuccess($accessLevel){
    	$this->smarty->assign("message", "You logged in successfully at <b>" .$accessLevel. "</b>");
//    	$this->smarty->assign("message", "The payed period is coming to end in 7 days. Please go to <a href=''>VOC Payment System</a> to pay for the next period.");
//    	    	$this->smarty->assign("color", "orange");
    	
    	$this->smarty->assign("color", "blue");
    }
    
    function loginError($accessLevel){
    	$this->smarty->assign("message", "Authorization failed!");
    	$this->smarty->assign("color", "orange");
    }
    
    function noProductsToGroup(){
    	$this->smarty->assign("message", "No products to group");
    	$this->smarty->assign("color", "orange");
    }
    
    function emptyCategory($categoryType, $bookmarkType=""){
	    switch ($categoryType) {
		    case "company":
			    $category="companies";
			    $this->smarty->assign("message", "No " .$category. " in the list");
			    break;
			    
		    case "facility":
			    $category="facilities";
			    $parentCategory="company";
			    $this->smarty->assign("message", "No " .$category. " in choosen " .$parentCategory);
			    break;
			    
		    case "department":
			    $category="departments";
			    $parentCategory="facility";
			    $this->smarty->assign("message", "No " .$category. " in choosen " .$parentCategory);
			    break;
			    
		    case "insideDepartment":
			    switch ($bookmarkType){
			    	case "product":			    	
					    $bookmark = "product";
					    $parentCategory = "company";
					    $this->smarty->assign("message", "No " .$bookmark. " in choosen " .$parentCategory);
					    break;
					    
				    case "equipment":
					    $bookmark="equipment";
					    $parentCategory="department";
					    $this->smarty->assign("message", "No " .$bookmark. " in choosen " .$parentCategory);
					    break;
					    
				    case "inventory":
					    $bookmark="inventories";
					    $parentCategory="facility";
					    $this->smarty->assign("message", "No " .$bookmark. " in choosen " .$parentCategory);
					    break;
					    
				    case "usage":
					    $bookmark="mixes";
					    $parentCategory="department";
					    $this->smarty->assign("message", "No " .$bookmark. " in choosen " .$parentCategory);
					    break;
			    }
			    
			    break;
			    
		    default: ;
	    }
	    $this->smarty->assign("color", "blue2");
    }
    
    function notSelected($categoryType){
	    switch ($categoryType) {
		    case "company":
			    $category="companies";
			    break;
			    
		    case "facility":
			    $category="facilities";
			    break;
			    
		    case "department":
			    $category="departments";
			    break;
			    
		    case "inventory":
			    $category="inventories";
			    break;
			    
		    case "equipment":
			    $category="equipment";
			    break;
			    
		    case "product":
			    $category="products";
			    break;
			    
		    case "usage":
			    $category="mixes";
			    break;
			    
		    default: ;
	    }
	    $this->smarty->assign("message", "No ".$category." selected");
	    $this->smarty->assign("color", "blue");
    }
    
    function productsInUse($productsInUse){
    	if (count($productsInUse)!=1){ 
	    	$message="Products ";
	    	$word="are";
    	} else {
	    	$message="Product ";
	    	$word="is";
    	}
	    if (count($productsInUse)>5) {
	    	$itemsCount=6;
	    } else {
	    	$itemsCount=count($productsInUse);
	    }
    	for ($i=0;$i<$itemsCount-1;$i++){
    		$message.="<b>".$productsInUse[$i]."</b>, ";
    	}
    	if (count($productsInUse)>5) {
	    	$message.="<b>...</b> ".$word." already in use";
    	} else {
    		$message.="<b>".$productsInUse[count($productsInUse)-1]."</b> ".$word." already in use";
    	}
		$this->smarty->assign("message", $message);
    	$this->smarty->assign("color", "orange");
		
    }
    
    
    function warnDeleteAdmin($categoryType, $categoryName = "") {
    	if ($categoryName=="") {
		    switch ($categoryType) {
			    case "apmethod":
				    $bookmark="AP methods";
				    break;
			    case "coat":
				    $bookmark="coat";
				    break;
			    case "product":
				    $bookmark="products";
				    break;
			    case "components":
				    $bookmark="components";
				    break;
			    case "density":
				    $bookmark="densities";
				    break;
			    case "country":
				    $bookmark="countries";
				    break;
			    case "state":
				    $bookmark="states";
				    break;
			    case "lol":
				    $bookmark="list of lists";
				    break;
			    case "msds":
				    $bookmark="MSDS";
				    break;
			    case "rule":
				    $bookmark="rules";
				    break;
			    case "substrate":
				    $bookmark="substrates";
				    break;
			    case "supplier":
				    $bookmark="suppliers";
				    break;
			    case "type":
				    $bookmark="types";
				    break;
			    case "unittype":
				    $bookmark="unittypes";
				    break;
			    case "formulas":
				    $bookmark="formulas";
				    break;
				case "agency":
				    $bookmark="agencies";
				    break;
				    
				case "company":
					$bookmark="users";
					break;
				case "facility":
					$bookmark="users";
					break;
				case "department":
					$bookmark="users";
					break;
				case "admin":
					$bookmark="users";
					break;
				    
			    default: ;
		    }
		    
		    $this->smarty->assign("message", "You are about to delete selected " .$bookmark. ". Are you sure?");
		    $this->smarty->assign("color", "orange");
	    } else {
		    switch ($categoryType) {
			    case "apmethod":
				    $bookmark="AP method";
				    break;
			    case "coat":
				    $bookmark="coat";
				    break;
			    case "product":
				    $bookmark="product";
				    break;
			    case "components":
				    $bookmark="component";
				    break;
			    case "density":
				    $bookmark="density";
				    break;
			    case "country":
				    $bookmark="country";
				    break;
			    case "state":
				    $bookmark="state";
				    break;
			    case "lol":
				    $bookmark="list of list";
				    break;
			    case "msds":
				    $bookmark="MSDS";
				    break;
			    case "rule":
				    $bookmark="rule";
				    break;
			    case "substrate":
				    $bookmark="substrate";
				    break;
			    case "supplier":
				    $bookmark="supplier";
				    break;
			    case "type":
				    $bookmark="type";
				    break;
				case "unittype":
				    $bookmark="unittype";
				    break;
				case "formulas":
				    $bookmark="formula";
				    break;
			    case "agency":
				    $bookmark="agency";
				    break;
				    
				case "company":
					$bookmark="user";
					break;
				case "facility":
					$bookmark="user";
					break;
				case "department":
					$bookmark="user";
					break;
				case "admin":
					$bookmark="user";
					break;
				    
			    default: ;
		    }
		    $this->smarty->assign("message", "You are about to delete " .$bookmark. " <b>" .$categoryName. "</b>. Are you sure?");
		    $this->smarty->assign("color", "orange");
	    }
    }
    
    function successDeletedAdmin($categoryType, $categoryNames) {
	    if (count($categoryNames)!=1) {
		    switch ($categoryType) {
			    case "apmethod":
				    $message="AP methods ";
				    break;
			    case "coat":
				    $message="Coat ";
				    break;
			    case "product":
				    $message="Products ";
				    break;
			    case "components":
				    $message="Components ";
				    break;
			    case "density":
				    $message="Densities ";
				    break;
			    case "country":
				    $message="Countries ";
				    break;
			    case "lol":
				    $message="List of lists ";
				    break;
			    case "msds":
				    $message="MSDS ";
				    break;
			    case "rule":
				    $message="Rules ";
				    break;
			    case "substrate":
				    $message="Substrates ";
				    break;
			    case "supplier":
				    $message="Suppliers ";
				    break;
			    case "type":
				    $message="Types ";
				    break;
			    case "unittype":
				    $message="Unittypes ";
				    break;
			    case "formulas":
				    $message="Formulas ";
				    break;
			    case "agency":
				    $message="Agencies ";
				    break;
				    
				case "company":
					$message="Users ";
					break;
				case "facility":
					$message="Users ";
					break;
				case "department":
					$message="Users ";
					break;
				case "admin":
					$message="Users ";
					break;
				
			    default: ;

		    }
		    $word="were";
	    } else {
		    switch ($categoryType) {
			    case "apmethod":
				    $message="AP method ";
				    break;
			    case "coat":
				    $message="Coat ";
				    break;
			    case "product":
				    $message="Product ";
				    break;
			    case "components":
				    $message="Component ";
				    break;
			    case "density":
				    $message="Density ";
				    break;
			    case "country":
				    $message="Country ";
				    break;
			    case "lol":
				    $message="List of list ";
				    break;
			    case "msds":
				    $message="MSDS ";
				    break;
			    case "rule":
				    $message="Rule ";
				    break;
			    case "substrate":
				    $message="Substrate ";
				    break;
			    case "supplier":
				    $message="Supplier ";
				    break;
			    case "type":
				    $message="Type ";
				    break;
			    case "unittype":
				    $message="Unittype ";
				    break;
			    case "formulas":
				    $message="Formula ";
				    break;
			    case "agency":
				    $message="Agency ";
				    break;
				    
				case "company":
					$message="User ";
					break;
				case "facility":
					$message="User ";
					break;
				case "department":
					$message="User ";
					break;
				case "admin":
					$message="User ";
					break;
				    
			    default: ;
		    }
		    $word="was";
	    }
	    
	    if (count($categoryNames)>5) {
	    	$itemsCount=6;
	    } else {
			$itemsCount=count($categoryNames);
		}
    	for ($i=0;$i<$itemsCount-1;$i++){
    		$message.="<b>".$categoryNames[$i]."</b>, ";
    	}
    	if (count($categoryNames)>5) {
	    	$message.="<b>...</b> ".$word." successfully deleted";
    	} else {
    		$message.="<b>".$categoryNames[count($categoryNames)-1]."</b> ".$word." successfully deleted";
    	}
		$this->smarty->assign("message", $message);
    	$this->smarty->assign("color", "green");
    }
    
    function successEditedAdmin($categoryType, $categoryName) {
	    switch ($categoryType) {
		    case "apmethod":
			    $bookmark="AP method";
			    break;
		    case "coat":
			    $bookmark="Coat";
			    break;
		    case "product":
			    $bookmark="Product";
			    break;
		    case "components":
			    $bookmark="Component";
			    break;
		    case "density":
			    $bookmark="Density";
			    break;
		    case "country":
			    $bookmark="Country";
			    break;
		    case "state":
			    $bookmark="State";
			    break;
		    case "lol":
			    $bookmark="List of list";
			    break;
		    case "msds":
			    $bookmark="MSDS";
			    break;
		    case "rule":
			    $bookmark="Rule";
			    break;
		    case "substrate":
			    $bookmark="Substrate";
			    break;
		    case "supplier":
			    $bookmark="Supplier";
			    break;
		    case "type":
			    $bookmark="Type";
			    break;
		    case "unittype":
			    $bookmark="Unittype";
			    break;
		    case "formulas":
			    $bookmark="Formula";
			    break;
		    case "agency":
			    $bookmark="Agency";
			    break;
			    
			case "issue":
			    $bookmark = "Issue";
			    break;
			    
		    default: ;
	    }
	    $this->smarty->assign("message", $bookmark. " <b>" .$categoryName. "</b> was successfully edited");
	    $this->smarty->assign("color", "green");
    }
    
    function successAddedAdmin($categoryType, $categoryName) {
	    switch ($categoryType) {
		    case "apmethod":
				$bookmark="AP method";
				break;
		    case "coat":
				$bookmark="Coat";
				break;
			case "product":
			    $bookmark="Product";
			    break;
		    case "components":
			    $bookmark="Component";
			    break;
		    case "density":
			    $bookmark="Density";
			    break;
		    case "country":
			    $bookmark="Country";
			    break;
		    case "state":
			    $bookmark="State";
			    break;
		    case "lol":
			    $bookmark="List of list";
			    break;
		    case "msds":
			    $bookmark="MSDS";
			    break;
		    case "rule":
			    $bookmark="Rule";
			    break;
		    case "substrate":
			    $bookmark="Substrate";
			    break;
		    case "supplier":
			    $bookmark="Supplier";
			    break;
		    case "type":
			    $bookmark="Type";
			    break;
		    case "unittype":
			    $bookmark="Unittype";
			    break;
		    case "formulas":
			    $bookmark="Formula";
			    break;
			case "user":
				$bookmark="User";
			    break;
			case "agency":
				$bookmark="Agency";
			    break;
			    
		    default: ;
	    }
	    
	    $this->smarty->assign("message", $bookmark. " <b>" .$categoryName. "</b> was successfully added");
	    $this->smarty->assign("color", "green");
    }
    
    public function showMessage($message, $color) {
    	$this->smarty->assign("globalMessage", $message);
	    $this->smarty->assign("globalColor", $color);
    }
    
    public function uploadComplete() {
    	$this->smarty->assign("message", "Upload complete!");
	    $this->smarty->assign("color", "green");
    }
    
    public function paymentNotify($daysLeft) {
    	if ($daysLeft == 1) {
    		$foramttedDaysLeft = "<b>1</b> day";
    	} else {
    		$foramttedDaysLeft = "<b>".$daysLeft."</b> days";
    	}
    	$vpsLink = "<a href='vps.php'>VOC Payment System</a>";
    	
    	$this->smarty->assign("message", "The payment period is coming to end in ".$foramttedDaysLeft.". Please, go to ".$vpsLink." pay for the next period.");
	    $this->smarty->assign("color", "orange");
    }
    
    public function billingPlanLimitations($limitName) {
    	$this->smarty->assign("message", "You cannot add new ".$limitName." according to your Billing Plan");
	    $this->smarty->assign("color", "orange");
    }
}
?>