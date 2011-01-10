<?php
class validateCSV {
	
	public $productsError;
	public $productsCorrect;
	public $errorComments;
	
	function validateCSV() {
		$this->productsError = array();
		$this->productsCorrect = array();
		$this->errorComments = "";
	}
	
	public function validate($input) {
		$CSVPath = $input['inputFile'];
		//last row
		$file = fopen($CSVPath, "a");
		
		fwrite($file,";;;;;;;;;;;;;;;;;;;;;;;;\n");
		fclose($file);
		
		$file = fopen($CSVPath, "r");
				
		$headerKey = $this->tableHeader($file); //identification columns by their header
		
		$row = 3;
		$lastNotEmptyRow = 4;
		$inProduct = false;
		$error = "";
		$this->errorComments = "--------------------------------\n";
		$this->errorComments .= "(" . date("m.d.Y H:i:s") . ") Starting validation of ". $input['realFileName'] . "...\n";
		while ($dat = fgetcsv($file, 1000, ";")){
			
			$data = Array();
			foreach ($dat as $val){
				$data[] = mysql_real_escape_string($val);
			}
			
			$data = $this->trimAll($data);			
			$data_tmp[0] = $data[$headerKey['productID']];
			$data_tmp[1] = $data[$headerKey['mfg']];
			$data_tmp[2] = $data[$headerKey['productName']];
			$data_tmp[3] = $data[$headerKey['type']];
			$data_tmp[4] = $data[$headerKey['scoating']];
			$data_tmp[5] = $data[$headerKey['aerosol']];
			$data_tmp[6] = $data[$headerKey['substrate']];
			$data_tmp[7] = $data[$headerKey['rule']];
			$data_tmp[8] = $data[$headerKey['vocwx']];
			$data_tmp[9] = $data[$headerKey['voclx']];
			$data_tmp[10] = $data[$headerKey['case']];
			$data_tmp[11] = $data[$headerKey['description']];
			$data_tmp[12] = $data[$headerKey['mmhg']];
			$data_tmp[13] = $data[$headerKey['temp']];
			$data_tmp[14] = $data[$headerKey['weight']];
			$data_tmp[15] = $data[$headerKey['density']];
			$data_tmp[16] = $data[$headerKey['gavity']];
			$data_tmp[18] = $data[$headerKey['boilingRangeFrom']];
			$data_tmp[19] = $data[$headerKey['boilingRangeTo']];
			$data_tmp[20] = $data[$headerKey['class']];
			$data_tmp[21] = $data[$headerKey['irr']];
			$data_tmp[22] = $data[$headerKey['ohh']];
			$data_tmp[23] = $data[$headerKey['sens']];
			$data_tmp[24] = $data[$headerKey['oxy1']];
			$data_tmp[25] = $data[$headerKey['VOCPM']];
			
			//uk
			//-----------------------------------------
			$data_tmp[26] = $data[$headerKey['einecsElincs']];
			$data_tmp[27] = $data[$headerKey['substanceSymbol']];
			$data_tmp[28] = $data[$headerKey['substanceR']];
			$data_tmp[29] = $data[$headerKey['percentVolatileWeight']];
			$data_tmp[30] = $data[$headerKey['percentVolatileVolume']];
			$data_tmp[31] = $data[$headerKey['waste']];
			
			$data = $data_tmp;			

				if (!empty($data[0])) {
					
					$componentKey = -1;
					
					if ($inProduct) {
						if ($error != "") {
							$product['errorComments'] = $error;
							$this->productsError[] = $product;					
						} else {
							$this->productsCorrect[] = $product;
						}					
						$inProduct = false;					
						$error = "";
					}
					
					$inProduct = true;
					
					$currRowComments = $this->productDataCheck($data,$row);
					if ($currRowComments != "") {
						//$error = TRUE;
						$error .= $currRowComments;
					}
					$this->errorComments .= $currRowComments;
					
					//	product processing
					$product = array (
						"productID" => $data[0],
						"MFG" => $data[1],
						"productName" => $data[2],
						"coating" => $data[3],
						"specialtyCoating" => $data[4],
						"aerosol" => $data[5],
						"vocwx" => $data[8],
						"voclx" => $data[9],
						"density" => $data[15],
						"gavity" => $data[16],
						"boilingRangeFrom" => $data[18],
						"boilingRangeTo" => $data[19],
						"hazardousClass" => $data[20],
						"hazardousIRR" => $data[21],
						"hazardousOHH" => $data[22],
						"hazardousSENS" => $data[23],
						"hazardousOXY" => $data[24],
						
						"percentVolatileWeight" => $data[29],
						"percentVolatileVolume" => $data[30],
						"waste" => $data[31],
					);				
				} 
				
				//	components processing
				if (!empty($data[10]) && $inProduct) {					
					if ($lastNotEmptyRow == $row - 1) {
						$productKeys = $this->productsKeys();						
						foreach ($data as $key=>$field) {
							foreach ($productKeys as $productKey) {
								if ($productKey == $key) {
									if ($field != "") {
										$map = $this->map();
										$lastNotEmptyRow = $row;
										$brokenLine = true;
										if ($map[$key] == 'substanceR' || $map[$key] == 'hazardousIRR' || $map[$key] == 'waste') {
											$product[$map[$key]] .= ",".$data[$key];
										} else {
											$product[$map[$key]] .= " ".$data[$key];	
										}																										
									}									
								}
							}							
						}												
					}
					
					$lastNotEmptyRow = $row;
					
					$currRowComments = $this->componentDataCheck($data,$row);
					if ($currRowComments != "") {
						$error .= $currRowComments;
					}
					$this->errorComments .= $currRowComments;										
					
					$component = array (
						"substrate" => $data[6],
						"rule" => $data[7],
						"caseNumber" => $data[10],
						"description" => $data[11],
						"mmhg" => $data[12],
						"temp" => $data[13],
						"weight" => $data[14],
						"vocpm" => $data[25],
						
						"einecsElincs" => $data[26],
						"substanceSymbol" => $data[27],
						"substanceR" => $data[28]
					);
					
					$product["component"][] = $component;
					$componentKey++;	
									
				} else {
										
					$brokenLine = false;
					
					if ($lastNotEmptyRow == $row - 1) {
						foreach ($data as $key=>$field) {
							if ($field != "") {
								$map = $this->map();
								$productKeys = $this->productsKeys();
								$componentsKeys = $this->componentsKeys();
								$lastNotEmptyRow = $row;
								$brokenLine = true;
								if ($map[$key] == 'substanceR' || $map[$key] == 'hazardousIRR' || $map[$key] == 'waste') {
									if (array_search($key, $productKeys)) {
										$product[$map[$key]] .= ",".$data[$key];	
									} else {
										$product['component'][$componentKey][$map[$key]] .= ",".$data[$key];
									}									
								} else {
									if (array_search($key, $productKeys)) {
										$product[$map[$key]] .= " ".$data[$key];	
									} else {
										$product['component'][$componentKey][$map[$key]] .= " ".$data[$key];
									}												
								}																										
							}
						}												
					}
					
					
					if (!$brokenLine) {						
						$tmpArray = $this->componentsKeys();
						$column = "";
						foreach ($tmpArray as $key) {
							if (!empty($data[$key])) {
								if ($column != "") {
									$column.=", ";
								}
								switch ($key) {
									case 6:
										$column.="substrate";
										break;
									case 7:
										$column.="rule";
										break;
									case 10:
										$column.="caseNumber";
										break;
									case 11:
										$column.="description";
										break;
									case 12:
										$column.="mmhg";
										break;
									case 13:
										$column.="temp";
										break;
									case 14:
										$column.="weight";
										break;
									case 25:
										$column.="vocpm";
										break;
										
									case 26:
										$column.="einecsElincs";
										break;
									case 27:
										$column.="substanceSymbol";
										break;
									case 28:
										$column.="substanceR";
										break;
								}								
							}						
						}
						if ($column != "") {						
							$error .= "	Can't assign component to product: Undefined data ".$column.". Row " . $row . ".\n";
							$this->errorComments .= "	Can't assign component to product: Undefined data ".$column.". Row " . $row . ".\n";
						}						
						
						if ($error != "" && $inProduct) {
							$product['errorComments'] = $error;
							$this->productsError[] = $product;					
						} else {
							if ($inProduct){
								$this->productsCorrect[] = $product;
							}
						}					
						$inProduct = false;									
						$error = "";	
					}																
				}						
			$row++;
		}		
		fclose($file);
						
	}
	
	
	private function productDataCheck($data,$row){
		$comments = "";
		//product id check
		if (strlen($data[0])>50) {			
			$comments .= "	Product ID value is too long. Row " . $row . ".\n";
		}
		
		//supplier check
		if (strlen($data[1])>200) {			
			$comments .= "	MFG value is too long. Row " . $row . ".\n";
		}
		
		//PRODUCT NAME/COLOR check
		if (strlen($data[2])>200) {
			$comments .= "	Product name value is too long. Row " . $row . ".\n";
		}
		
		//coating check
		if (strlen($data[3])>50) {
			$comments .= "	Coating value is too long. Row " . $row . ".\n";
		}
		
		//specialty coating check
		//$sc = trim($data[4]);		
		if ( !(strtoupper($data[4]) == "YES" || strtoupper($data[4]) == "NO" || empty($data[4])) ) {
			$comments .= "	Specialty coating value is undefined. Row " . $row . ".\n";
		}
		
		//aerosol check
		//$aerosol = trim($data[5]);
		if ( !(strtoupper($data[5]) == "YES" || strtoupper($data[5]) == "NO" || empty($data[5])) ) {
			$comments .= "	Aerosol value is undefined. Row " . $row . ".\n";
		}
		
		//vocwx check
		$data[8] = str_replace(",",".",$data[8]);
		if ( !preg_match("/^[0-9.]*$/",$data[8]) || (substr_count($data[8],".") > 1) ){
			$comments .= "	VOCWX is undefined. Row " . $row . ".\n";	
		}
		
		//voclx check
		$data[9] = str_replace(",",".",$data[9]);
		if ( !preg_match("/^[0-9.]*$/",$data[9]) || (substr_count($data[9],".") > 1) ){
			$comments .= "	VOCLX is undefined. Row " . $row . ".\n";	
		}
		
		//density check		
		$data[15] = str_replace(",",".",$data[15]);
		if ( !preg_match("/^[0-9.]*$/",$data[15]) || (substr_count($data[15],".") > 1) ){
			$comments .= "	Density is undefined. Row " . $row . ".\n";	
		}
		
		//gavity check		
		$data[16] = str_replace(",",".",$data[16]);
		if ( !preg_match("/^[0-9.]*$/",$data[16]) || (substr_count($data[16],".") > 1) ){
			$comments .= "	Specific Gavity is undefined. Row " . $row . ".\n";
		}
		
		//boiling range check		
		$data[18] = str_replace(",",".",$data[18]);
		if ( !preg_match("/^[0-9.]*$/",$data[18]) || (substr_count($data[18],".") > 1) ){
			$comments .= "	Boiling Range From is undefined. Row " . $row . ".\n";
		}
		if (empty($data[18]) && ($data[18] !== '0') ){
			$comments .= "	Boiling Range From is empty. Row " . $row . ".\n";			
		}
		
		$data[19] = str_replace(",",".",$data[19]);
		if ( !preg_match("/^[0-9.]*$/",$data[19]) || (substr_count($data[19],".") > 1) ){
			$comments .= "	Boiling Range To is undefined. Row " . $row . ".\n";
		}
		if (empty($data[19]) && ($data[19] !== '0') ){
			$comments .= "	Boiling Range To is empty. Row " . $row . ".\n";
		}
		
		//hazardous class check
		if (strlen($data[20])>64) {
			$comments .= "	Hazardous class value is too long. Row " . $row . ".\n";
		}
		//if (empty($data[20]) && ($data[20] !== '0')){
		//	$comments .= "	Hazardous class is empty. Row " . $row . ".\n";
		//}
		
		
		//percent volatile by weight
		$data[29] = str_replace(",",".",$data[29]);
		if ( !preg_match("/^[0-9.]*$/",$data[29]) || (substr_count($data[29],".") > 1) || $data[29] > 100 ){
			$comments .= "	Percent Volatile by Weight is undefined. Row " . $row . ".\n";	
		}
		//percent volatile by volume
		$data[30] = str_replace(",",".",$data[30]);
		if ( !preg_match("/^[0-9.]*$/",$data[30]) || (substr_count($data[30],".") > 1) || $data[30] > 100 ){
			$comments .= "	Percent Volatile by Weight is undefined. Row " . $row . ".\n";	
		}
		
		//waste class
		$data[31] = str_replace("/",",R",$data[31]);
		$wasteArray = explode(",",$data[31]);		
		foreach($wasteArray as $waste) {
			$waste = trim($waste);
			if (strlen($waste)>20) {			
				$comments .= "	Waste value is too long. Row " . $row . ".\n";
			}
		}
						
		return $comments;
	}
	
	
	private function componentDataCheck($data,$row) {
		$comments = "";
		
		//substrate check
		if (strlen($data[6])>200) {			
			$comments .= "	Substrate value is too long. Row " . $row . ".\n";
		}
		
		//rule check
		if (empty($data[7])) {			
			//$comments .= "Rule is empty. Row " . $row . "\n";  //check for NULL value is not required?
		} elseif (!preg_match("/^[0-9]+$/",$data[7])){
			$comments .= "	Rule is undefined. Row " . $row . ".\n";
		}
		
		//cas check
		/*if ( !preg_match("/^[0-9\-]*$/",$data[10]) ){ //check by pattern is not required?
		 $comments .= "CASE number is undefined. Row " . $row . "\n";	
		 }*/
		if (strlen($data[10])>128) {			
			$comments .= "	CASE number value is too long. Row " . $row . ".\n";
		}
		if (empty($data[10]) && ($data[10] !== '0')) {
			$comments .= "	CASE number is empty. Row " . $row . ".\n";			
		}
		
		//comp description check
		if (strlen($data[11])>128) {			
			$comments .= "	Description value is too long. Row " . $row . ".\n";
		}
		if (empty($data[11]) && ($data[11] !== '0')) {
			$comments .= "	Description is empty. Row " . $row . ".\n";			
		}
		
		// mm/hg check
		$data[12] = str_replace(",",".",$data[12]);
		if ( !preg_match("/^[0-9.]*$/",$data[12]) || (substr_count($data[12],".") > 1) ){
			$comments .= "	MM/HG is undefined. Row " . $row . ".\n";	
		}
		
		//temp check		
		$data[13] = str_replace(",",".",$data[13]);
		$data[13] = str_replace("C","",$data[13]);
		$data[13] = str_replace("c","",$data[13]);
		$data[13] = trim ($data[13]);
		if ( !preg_match("/^[0-9]*\.*[0]*$/",$data[13]) || (substr_count($data[13],".") > 1) ){
			$comments .= "	Temp is undefined. Row " . $row . ".\n";	
		}
		
		//weight check		
		$data[14] = str_replace(",",".",$data[14]);
		$data[14] = str_replace("%","",$data[14]);
		$data[14] = trim ($data[14]);
		if ( !preg_match("/^[0-9]*\.*[0]*$/",$data[13]) || (substr_count($data[13],".") > 1) ){
			$comments .= "	Temp is undefined. Row " . $row . ".\n";	
		}
		
		//vocpm check		
		if ( !(strtoupper($data[25]) == "VOC" || strtoupper($data[25]) == "PM" || empty($data[25])) ) {
			$comments .= "	VOC/PM value is undefined. Row " . $row . ".\n";
		}
		
		//einecs check		
		if (strlen($data[26])>128) {			
			$comments .= "	Einecs/elincs value is too long. Row " . $row . ".\n";
		}
		
		//substance symbol check		
		if (strlen($data[27])>10) {			
			$comments .= "	Symbol of substance value is too long. Row " . $row . ".\n";
		}	
		
		//substance r check		
		$data[28] = str_replace("/",",R",$data[28]);
		$rArray = explode(",",$data[28]);
		foreach($rArray as $r) {
			$r = trim($r);
			if (strlen($r)>32) {			
				$comments .= "	R(*) of substance value is too long. Row " . $row . ".\n";
			}
		}								
		
		return $comments;
	}
	
	
	private function trimAll($data){
		for($i=0;$i<count($data);$i++) {
			$data[$i] = trim($data[$i]);			
		}
		return $data;
	}
	
	
	private function tableHeader($file){
		$dat = fgetcsv($file, 1000, ";");
		$firstRowData = Array();
		foreach ($dat as $val){
			$firstRowData[] = mysql_real_escape_string($val);
		}
		$dat = fgetcsv($file, 1000, ";");
		$secondRowData = Array();
		foreach ($dat as $val){
			$secondRowData[] = mysql_real_escape_string($val);
		}
		
		//possible headers variations
		$possibleProductID = array ('PRODUCT ID','PRODUCTID','PRODUCT_ID');
		$possibleMFG = array ('MFG','MANUFACTURER','SUPPLIER','PRODUCER');
		$possibleProductName = array ('PRODUCT NAME/COLOR','PRODUCT NAME','COLOR','PRODUCTNAME/COLOR','PRODUCT_NAME/COLOR');
		$possibleType = array('COATING','COAT','TYPE');
		$possibleSpecCoating = array('SPECIALTY COATING','SPECIALTY_COATING','COATING (SPECIALTY)','COATING SPECIALTY');
		$possibleAerosol = array('AEROSOL','AIROSOL');
		$possibleSubstrate = array('SUBSTRATE','SUB STRATE','SUB_STRATE');
		$possibleRule = array('RULE','RULES');
		$possibleVocwx = array('VOCWX','MATERIAL VOC','MATERIAL_VOC','MATERIAL VOCWX','VOCWX MATERIAL');
		$possibleVoclx = array('VOCLX','COATING VOC','COATING_VOC','COATING VOCLX','VOCLX COATING');
		$possibleCase = array('CASE NUMBER','CAS NUMBER','CASE_NUMBER','NUMBER CASE','NUMBER CAS');
		$possibleDesription = array('DESCRIPTION','DESC');
		$possibleMMHG = array('MMHG','MM/HG','MM\\HG');
		$possibleTemp = array('TEMP','TMP','TEMPERATURE');
		$possibleWeight = array('WEIGHT','WEIGHT %', 'WEIGHT,%','WEIGHT, %');
		$possibleDensity = array('DENSITY','DENSITY LBS/GAL','DENSITY, LBS/GAL','DENSITY LBS/GAL US','US DENSITY LBS/GAL');
		$possibleVOCPM = array('VOC/PM','VOCPM','VOC PM', 'VOC\\PM');
		
		$possibleEinecsElinks = array('IENECS','ELINCS','IENECS/ELINCS','IENECS / ELINCS', 'IENECS/ ELINCS','IENECS /ELINCS',
										'IENECS\\ELINCS','IENECS \\ ELINCS','IENECS\\ ELINCS', 'IENECS \\ELINCS');
		$possibleSubstanceSymbol = array('SYMBOL OF SUBSTANCE','SYMBOL', 'SYMBOL OF');
		$possibleSubstanceR = array('R(*) OF SUBSTANCE','RULE OF SUBSTANCE','R OF', 'R(*) OF', 'R (*) OF', 'R', 'R(*)', 'R (*)');
		$possiblePercentVolatile = array();
		
		$columnIndex = array();
		
		for ($i=0;$i<count($secondRowData);$i++){
			$columnIndex[$i] = FALSE;
			//PRODUCT ID mapping
			if (!isset($key['productID'])){
				if ( strtoupper(trim($firstRowData[$i])) == 'PRODUCT' && strtoupper(trim($secondRowData[$i])) == 'ID' ) {
					$key['productID'] = $i;
					$columnIndex[$i] = TRUE;
				}
				foreach ($possibleProductID as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['productID'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['productID'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//MFG mapping
			if (!isset($key['mfg'])){				
				foreach ($possibleMFG as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['mfg'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['mfg'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}											
			
			//PRODUCT NAME/COLOR mapping
			if (!isset($key['productName'])){
				if ( strtoupper(trim($firstRowData[$i])) == 'PRODUCT NAME' && strtoupper(trim($secondRowData[$i])) == 'COLOR' ) {
					$key['productName'] = $i;
					$columnIndex[$i] = TRUE;
				}
				foreach ($possibleProductName as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['productName'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['productName'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//TYPE mapping
			if (!isset($key['type'])){				
				foreach ($possibleType as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header && empty($secondRowData[$i]) ){
						$key['type'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header && empty($firstRowData[$i]) ){
						$key['type'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//Spec Coating mapping
			if (!isset($key['scoating'])){
				if ( strtoupper(trim($firstRowData[$i])) == 'SPECIALTY' && strtoupper(trim($secondRowData[$i])) == 'COATING' ) {
					$key['scoating'] = $i;
					$columnIndex[$i] = TRUE;
				}
				if ( strtoupper(trim($firstRowData[$i])) == 'COATING' && strtoupper(trim($secondRowData[$i])) == 'SPECIALTY' ) {
					$key['scoating'] = $i;
					$columnIndex[$i] = TRUE;
				}
				foreach ($possibleSpecCoating as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['scoating'] = $i;	
						$columnIndex[$i] = TRUE;								
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['scoating'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//aerosol mapping
			if (!isset($key['aerosol'])){				
				foreach ($possibleAerosol as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['aerosol'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['aerosol'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//substrate mapping
			if (!isset($key['substrate'])){
				if ( strtoupper(trim($firstRowData[$i])) == 'SUB' && strtoupper(trim($secondRowData[$i])) == 'STRATE' ) {
					$key['substrate'] = $i;
					$columnIndex[$i] = TRUE;
				}		
				foreach ($possibleSubstrate as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['substrate'] = $i;	
						$columnIndex[$i] = TRUE;								
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['substrate'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//rule mapping
			if (!isset($key['rule'])){				
				foreach ($possibleRule as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['rule'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['rule'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//vocwx mapping
			if (!isset($key['vocwx'])){
				if ( strtoupper(trim($firstRowData[$i])) == 'MATERIAL' && 
						(strtoupper(trim($secondRowData[$i])) == 'VOC' || strtoupper(trim($secondRowData[$i])) == 'VOCWX') ) {
					$key['vocwx'] = $i;
					$columnIndex[$i] = TRUE;
				}
				foreach ($possibleVocwx as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['vocwx'] = $i;		
						$columnIndex[$i] = TRUE;							
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['vocwx'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//voclx mapping
			if (!isset($key['voclx'])){
				if ( strtoupper(trim($firstRowData[$i])) == 'COATING' && 
						(strtoupper(trim($secondRowData[$i])) == 'VOC' || strtoupper(trim($secondRowData[$i])) == 'VOCWX') ) {
					$key['voclx'] = $i;
					$columnIndex[$i] = TRUE;
				}
				foreach ($possibleVoclx as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['voclx'] = $i;	
						$columnIndex[$i] = TRUE;								
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['voclx'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//case number mapping
			if (!isset($key['case'])){
				if ( (strtoupper(trim($firstRowData[$i])) == 'CASE' || strtoupper(trim($firstRowData[$i])) == 'CAS' ) && 
						strtoupper(trim($secondRowData[$i])) == 'NUMBER' ) {
					$key['case'] = $i;
					$columnIndex[$i] = TRUE;
				}
				foreach ($possibleCase as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['case'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['case'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//description number mapping
			if (!isset($key['description'])){				
				foreach ($possibleDesription as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['description'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['description'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//mm/hg number mapping
			if (!isset($key['mmhg'])){				
				foreach ($possibleMMHG as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['mmhg'] = $i;	
						$columnIndex[$i] = TRUE;								
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['mmhg'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//temp number mapping
			if (!isset($key['temp'])){				
				foreach ($possibleTemp as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['temp'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['temp'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//weight mapping
			if (!isset($key['weight'])){				
				foreach ($possibleWeight as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['weight'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['weight'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//density mapping
			if (!isset($key['density'])){				
				foreach ($possibleDensity as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['density'] = $i;	
						$columnIndex[$i] = TRUE;								
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['density'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}
			}
			
			//s gavity mapping
			if (!isset($key['gavity'])){								
				if ( strtoupper(trim($firstRowData[$i])) == 'GAVITY' ){
					$key['gavity'] = $i;
					$columnIndex[$i] = TRUE;									
				} elseif ( strtoupper(trim($secondRowData[$i])) == 'GAVITY' ){
					$key['gavity'] = $i;
					$columnIndex[$i] = TRUE;
				}					
			}
			
			//boiling range from/to mapping
			if (!isset($key['boilingRangeFrom'])){								
				if ( strtoupper(trim($firstRowData[$i])) == 'BOILING RANGE' && strtoupper(trim($secondRowData[$i])) == 'FROM' ){
					$key['boilingRangeFrom'] = $i;
					$key['boilingRangeTo'] = $i+1;
					$columnIndex[$i] = TRUE;
					$columnIndex[$i+1] = TRUE;
				} elseif ( strtoupper(trim($firstRowData[$i])) == 'BOILING RANGE' && strtoupper(trim($secondRowData[$i])) == 'TO' ) {
					$key['boilingRangeFrom'] = $i-1;
					$key['boilingRangeTo'] = $i;
					$columnIndex[$i-1] = TRUE;
					$columnIndex[$i] = TRUE;					
				}
			}
			
			//class mapping
			if (!isset($key['class'])){								
				if ( strtoupper(trim($secondRowData[$i])) == 'CLASS' ){
					$key['class'] = $i;
					$columnIndex[$i] = TRUE;
				}					
			}
			
			//irr mapping
			if (!isset($key['irr'])){								
				if ( strtoupper(trim($secondRowData[$i])) == 'IRR' ){
					$key['irr'] = $i;
					$columnIndex[$i] = TRUE;
				}					
			}
			
			//ohh mapping
			if (!isset($key['ohh'])){								
				if ( strtoupper(trim($secondRowData[$i])) == 'OHH' ){
					$key['ohh'] = $i;
					$columnIndex[$i] = TRUE;
				}					
			}
			
			//sens mapping
			if (!isset($key['sens'])){								
				if ( strtoupper(trim($secondRowData[$i])) == 'SENS' ){
					$key['sens'] = $i;
					$columnIndex[$i] = TRUE;
				}					
			}
			
			//sens mapping
			if (!isset($key['oxy1'])){								
				if ( strtoupper(trim($secondRowData[$i])) == 'OXY-1' ){
					$key['oxy1'] = $i;
					$columnIndex[$i] = TRUE;
				}					
			}
			
			//voc/pm mapping
			if (!isset($key['VOCPM'])){								
				foreach ($possibleVOCPM as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['VOCPM'] = $i;
						$columnIndex[$i] = TRUE;									
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['VOCPM'] = $i;
						$columnIndex[$i] = TRUE;
					}
				}						
			}		
			
			//	einecs/elincs mapping
			if (!isset($key['einecsElincs'])){															
				foreach ($possibleEinecsElinks as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['einecsElincs'] = $i;	
						$columnIndex[$i] = TRUE;								
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['einecsElincs'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}			
			}	
						
			//	substance symbol mapping
			if (!isset($key['substanceSymbol'])){															
				foreach ($possibleSubstanceSymbol as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['substanceSymbol'] = $i;	
						$columnIndex[$i] = TRUE;								
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['substanceSymbol'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}			
			}
						
			//	substance r mapping
			if (!isset($key['substanceR'])){															
				foreach ($possibleSubstanceR as $header){
					if ( strtoupper(trim($firstRowData[$i])) == $header ){
						$key['substanceR'] = $i;	
						$columnIndex[$i] = TRUE;								
					} elseif ( strtoupper(trim($secondRowData[$i])) == $header ){
						$key['substanceR'] = $i;
						$columnIndex[$i] = TRUE;
					}	
				}			
			}		
			
			//	Percent Volatile mapping
			if (!isset($key['percentVolatileWeight'])){																		
				if ( strtoupper(trim($firstRowData[$i])) == 'PERCENT VOLATILE' && strtoupper(trim($secondRowData[$i])) == 'BY WEIGHT' ){
					$key['percentVolatileWeight'] = $i;
					$key['percentVolatileVolume'] = $i+1;
					$columnIndex[$i] = TRUE;
					$columnIndex[$i+1] = TRUE;
				} elseif ( strtoupper(trim($firstRowData[$i])) == 'PERCENT VOLATILE' && strtoupper(trim($secondRowData[$i])) == 'BY VOLUME' ) {
					$key['percentVolatileWeight'] = $i-1;
					$key['percentVolatileVolume'] = $i;
					$columnIndex[$i-1] = TRUE;
					$columnIndex[$i] = TRUE;					
				}			
			}		
			
			
			//waste mapping
			if (!isset($key['waste'])){								
				if ( strtoupper(trim($secondRowData[$i])) == 'WASTE' ){
					$key['waste'] = $i;
					$columnIndex[$i] = TRUE;
				}					
			}
			
					
		}
		
		$columnsArray = array ('productID','mfg','productName','type','scoating','aerosol','substrate',
								'rule','vocwx','voclx','case','description','mmhg','temp','weight',
								'density','gavity','boilingRangeFrom','boilingRangeTo','class','irr',
								'ohh','sens','oxy1','VOCPM', 'einecsElincs','substanceSymbol', 'substanceR',
								'percentVolatileWeight', 'percentVolatileVolume', 'waste');
		for ($i=0;$i<count($columnsArray);$i++){
			if ( !isset($key[$columnsArray[$i]]) && !$columnIndex[$i]){
				$key[$columnsArray[$i]] = $i;
			} elseif ( !isset($key[$columnsArray[$i]]) ){
				for ($j=0;$j<count($secondRowData);$j++){
					if (!$columnIndex[$j]){
						$key[$columnsArray[$i]] = $j;
						break;						
					}
				}							
			}			
		}
		
		return $key;
	}
	
	
	
	private function map() {
		return array (
			0 	=> "productID",
			1	=> "MFG",
			2	=> "productName",
			3	=> "coating",
			4	=> "specialtyCoating",
			5	=> "aerosol",
			6	=> "substrate",
			7	=> "rule",
			8	=> "vocwx",
			9	=> "voclx",
			10	=> "caseNumber",
			11	=> "description",
			12	=> "mmhg",
			13	=> "temp",
			14	=> "weight",
			15	=> "density",
			16	=> "gavity",
			18	=> "boilingRangeFrom",
			19	=> "boilingRangeTo",
			20	=> "hazardousClass",
			21	=> "hazardousIRR",
			22	=> "hazardousOHH",
			23	=> "hazardousSENS",
			24	=> "hazardousOXY",
			25	=> "vocpm",
			
			26	=> "einecsElincs",
			27	=> "substanceSymbol",
			28	=> "substanceR",			
			29	=> "percentVolatileWeight",
			30	=> "percentVolatileVolume",
			31	=> "waste",
						
		);				
	}
	
	
	private function componentsKeys() {
		return array(6,7,10,11,12,13,14,25,26,27,28);
	} 
	private function productsKeys() {
		return array(0,1,2,3,4,5,8,9,15,16,17,18,19,20,21,22,23,24,29,30,31);
	} 
		 
}

?>