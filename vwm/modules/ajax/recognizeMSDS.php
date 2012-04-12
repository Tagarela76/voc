<?php

chdir('../..');	

require('config/constants.php');
require_once ('modules/xnyo/xnyo.class.php');

$site_path = getcwd().DIRECTORY_SEPARATOR; 
define ('site_path', $site_path);
	
//	Include Class Autoloader
require_once('modules/classAutoloader.php');

$xnyo = new Xnyo;

$xnyo->database_type	= DB_TYPE;
$xnyo->db_host 			= DB_HOST;
$xnyo->db_user			= DB_USER;
$xnyo->db_passwd		= DB_PASS;

$xnyo->start();

$db->select_db(DB_NAME);

$uploads_dir = "../msds";

$xnyo->filter_get_var('count', 'int');
$count = $_GET['count'];
$names = array();
for ($i=0;$i<$count;$i++) {
	$xnyo->filter_get_var('name_'.$i, 'text');
	$names[] = $_GET['name_'.$i];	
}

//getting real_name

if ( $folderHandler = opendir($uploads_dir) ) {	
	while ( false !== $file = readdir($folderHandler) ) {
		if (!($file == "." || $file == "..")) {
			$filesFromDir[] = $file;									
		}								        		   		       				
	}
	closedir($folderHandler);
}    	

for($i=0;$i<count($filesFromDir);$i++){
	$_pos = strripos($filesFromDir[$i],"_");	 	
	$alreadyUploadedSheet['index'][$i] = substr($filesFromDir[$i],$_pos+1,strripos($filesFromDir[$i],".")-$_pos-1);   		
	$alreadyUploadedSheet['ext'][$i] = substr($filesFromDir[$i],strripos($filesFromDir[$i],".")+1);     
	$alreadyUploadedSheet['name'][$i] = substr($filesFromDir[$i],0,$_pos).".".$alreadyUploadedSheet['ext'][$i];        	
}    	    	
//distinct files. keep files with max index
$distinctFilesName = array_unique($alreadyUploadedSheet['name']);
foreach ($distinctFilesName as $distinctFileName) {
	$tmp[] = $distinctFileName;
}
$distinctFilesName = $tmp;

for($i=0;$i<count($distinctFilesName);$i++){
	$indexes = array();
	for($j=0;$j<count($alreadyUploadedSheet['name']);$j++){        		
		if ($distinctFilesName[$i] == $alreadyUploadedSheet['name'][$j]) {
			$indexes[] = $alreadyUploadedSheet['index'][$j];        		
		}
	}
	$alreadyUploadedSheets['index'][$i] = max($indexes);
	$alreadyUploadedSheets['ext'][$i] = substr($distinctFilesName[$i],strripos($distinctFilesName[$i],".")+1);
	$alreadyUploadedSheets['name'][$i] = $distinctFilesName[$i];
	foreach($names as $name) {
		if ($alreadyUploadedSheets['name'][$i] == $name) {
			$real_name = cutFileExtension($alreadyUploadedSheets['name'][$i])."_".$alreadyUploadedSheets['index'][$i].".".$alreadyUploadedSheets['ext'][$i];
			$msdsProduct['name'] = $name;
			$msdsProduct['real_name'] = $real_name;
			$msdsProducts[] = $msdsProduct;
		}
	}		
}        
unset($alreadyUploadedSheet);

//getting product List
$xnyo->filter_get_var('companyID','int');
$product = new Product($db);
$productList = $product->getFormatedProductList($_GET['companyID']);
//	NICE PRODUCT LIST 
foreach ($productList as $oneProduct) {
	$formattedProductList[$oneProduct['supplier']][] = $oneProduct;
}						
//$productList = $product->getProductList($_GET['companyID']);
//foreach ($productList as $productInfo) {
//	if (!isAlreadyAssigned($productInfo['product_id'], $db)) {
//		$tmp['formattedName'] = $productInfo['product_id']."  ".$productInfo['supplier']."  ".$productInfo['product_nr']."  ".$productInfo['coating'];
//		$tmp['id'] =  $productInfo['product_id'];
//		$formattedProductList[] = $tmp;	
//	}	
//}

//recognize
$countRec = 0;
$countUnrec = 0;

for($i=0;$i<count($msdsProducts);$i++) {
	
	$msdsName = cutFileExtension($msdsProducts[$i]["name"]);	
	$query = "SELECT product_id, product_nr FROM ".TB_PRODUCT." WHERE product_nr = '".$msdsName."'";
	$db->query($query);
	
	$maxRank = 0;
	$productID = "";
	foreach ($db->fetch_all() as $product) {		
		$productRank = getProductRank($product->product_nr, $msdsName);
		
		if ($productRank > $maxRank) {
			$maxRank = $productRank;
			$productID = $product->product_id;
			
			if ($productRank == 1) {
				break;
			}
		}
	}
	
	$msdsProducts[$i]["product_id"] = $productID;
	
	if ($msdsProducts[$i]["product_id"] == "") {
		$msdsProducts[$i]["isRecognized"] = false;
	} else {
		$msdsProducts[$i]["isRecognized"] = true;
	}
	
	$obj = "var tbody = document.getElementById('assignTable').getElementsByTagName('TBODY')[0];\n";
	$obj .= "var row = document.createElement('TR');\n";
	$obj .= "var td1 = document.createElement('TD');\n";
	$obj .=	"td1.appendChild(document.createTextNode('".$msdsProducts[$i]['name']."'));\n";
	$obj .= "var select = document.createElement('SELECT');\n";
	$obj .= "select.className = 'addInventory';\n";
	if ($msdsProducts[$i]['isRecognized'] == 1) {
		$obj .= "select.name = 'product2sheetRec_".$countRec."';\n";
		$obj .= "var inputSheetRec = document.createElement('INPUT');\n";
		$obj .= "var inputSheetRecRealName = document.createElement('INPUT');\n";
		$obj .= "inputSheetRec.type = 'hidden';\n";
		$obj .= "inputSheetRec.name = 'sheetRec_".$countRec."';\n";
		$obj .= "inputSheetRec.value = '".$msdsProducts[$i]['name']."';\n";
		$obj .= "inputSheetRecRealName.type = 'hidden';\n";
		$obj .= "inputSheetRecRealName.name = 'sheetRecRealName_".$countRec."';\n";
		$obj .= "inputSheetRecRealName.value = '".$msdsProducts[$i]['real_name']."';\n";
		
		$obj .= "var td2 = document.createElement('TD');\n";
		$obj .=	"td2.appendChild (select);\n";
		$obj .= "td2.appendChild (inputSheetRec);\n";
		$obj .= "td2.appendChild (inputSheetRecRealName);\n";
		$countRec++;
	} else {
		$obj .= "select.name = 'product2sheetUnrec_".$countUnrec."';\n";
		
		$obj .= "var inputSheetUnrec = document.createElement('INPUT');\n";
		$obj .= "var inputSheetUnrecRealName = document.createElement('INPUT');\n";
		$obj .= "inputSheetUnrec.type = 'hidden';\n";
		$obj .= "inputSheetUnrec.name = 'sheetUnrec_".$countUnrec."';\n";
		$obj .= "inputSheetUnrec.value = '".$msdsProducts[$i]['name']."';\n";
		$obj .= "inputSheetUnrecRealName.type = 'hidden';\n";
		$obj .= "inputSheetUnrecRealName.name = 'sheetUnrecRealName_".$countUnrec."';\n";
		$obj .= "inputSheetUnrecRealName.value = '".$msdsProducts[$i]['real_name']."';\n";
		
		$obj .= "var td2 = document.createElement('TD');\n";
		$obj .=	"td2.appendChild (select);\n";
		$obj .= "td2.appendChild (inputSheetUnrec);\n";
		$obj .= "td2.appendChild (inputSheetUnrecRealName);\n";
		$countUnrec++;
	}               	    
	
	$obj .= "select.options[select.options.length] = new Option('none','');\n";
	//for ($j=0; $j<count($formattedProductList); $j++) {
		foreach ($formattedProductList as $supplier=>$products) {
			$obj .= "var supplier = document.createElement('optgroup');\n";
			$obj .= "supplier.label = '".$supplier."';\n";
			
			foreach ($products as $product) {
				if ($msdsProducts[$i]['product_id'] != $product['product_id']) {
					$obj .= "var oOption = document.createElement('option');\n";
					$obj .= "oOption.value = '".$product['product_id']."';\n";
					$obj .= "oOption.text = '".$product['formattedProduct']."';\n";
					//$obj .= "supplier.options[select.options.length] = new Option('";
					//$obj .= $formattedProductList[$j]['formattedName']."', '";
					//$obj .= $formattedProductList[$j]['id']."');\n";					
				} else {
					$obj .= "var oOption = document.createElement('option');\n";
					$obj .= "oOption.value = '".$product['product_id']."';\n";
					$obj .= "oOption.text = '".$product['formattedProduct']."';\n";
					$obj .= "oOption.selected = true;\n";
					//$obj .= "select.options[select.options.length] = new Option('";
					//$obj .= $formattedProductList[$j]['formattedName']."', '";
					//$obj .= $formattedProductList[$j]['id']."', ";
					//$obj .= "true,true);\n";
				}
				$obj .= "supplier.appendChild(oOption);\n";
			}
			$obj .= "select.appendChild(supplier);\n";
		}			
	//}
	
	$obj .= "document.getElementById('sheetRecCount').value = '".$countRec."';\n";
	$obj .= "document.getElementById('sheetUnrecCount').value = '".$countUnrec."';\n";
	$obj .= "row.appendChild(td1);\n";
    $obj .=	"row.appendChild(td2);\n";	
    $obj .= "tbody.appendChild(row);\n";		
	echo $obj;
}	

function getProductIDByMSDS($msdsName) {
	$msdsName = cutFileExtension($msdsName);	
	
	$query = "SELECT product_id, product_nr FROM ".TB_PRODUCT." WHERE 1";
	$db->query($query);
	
	$maxRank = 0;
	$productID = "";
	foreach ($db->fetch_all() as $product) {
		$productRank = getProductRank($product->product_nr, $msdsName);
		
		if ($productRank > $maxRank) {
			$maxRank = $productRank;
			$productID = $product->product_id;
			
			if ($productRank == 1) {
				break;
			}
		}
	}
	
	return $productID;
}


function cutFileExtension($fileName) {
	$spotIndex = strripos($fileName, ".");
	
	if (!($spotIndex === false)) {
		$fileName = substr($fileName, 0, $spotIndex);
	}
	
	return $fileName;
}

function getProductRank($productNR, $msdsName) {
	$msdsName = strtolower(trim($msdsName));
	$productNR = strtolower(trim($productNR));
	
	if ($msdsName == $productNR) {
		return 1;
	} elseif (str_replace(" ", "", $msdsName) == str_replace(" ", "", $productNR)) {
		return 0.9;
	} elseif (str_replace(array("-", " "), "", $msdsName) == str_replace(array("-", " "), "", $productNR)) {
		return 0.85;
	}
	
	return 0;
}

function isAlreadyAssigned($productID, $db) {
	if (getAssignedMSDS($productID, $db) != "") {
		return true;
	} else {
		return false;
	}
}
	
function getAssignedMSDS($productID, $db) {	
	$query = "SELECT msds_file_id FROM ".TB_MSDS_FILE." WHERE product_id = ".$productID." LIMIT 1";
	$db->query($query);
	
	if ($db->num_rows()) {
		return $db->fetch(0)->msds_file_id;
	} else {
		return "";
	}
}
?>
