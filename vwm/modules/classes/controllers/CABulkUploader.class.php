<?php

class CABulkUploader extends Controller {

	function CABulkUploader($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'bulkUploader';
		$this->parent_category = 'bulkUploader';
	}

	function runAction() {
		$this->runCommon('admin');
		$functionName = 'action' . ucfirst($this->action);
		if (method_exists($this, $functionName))
			$this->$functionName();
	}

	private function actionBrowseCategory() {
		$title = new Titles($this->smarty);
		$title->titleBulkUploaderSettings();

		$company = new Company($this->db);
		$companyList = $company->getCompanyList();
		$companyList[] = array('id' => 0, 'name' => 'no company');
		$this->smarty->assign('companyList', $companyList);
		$this->smarty->assign('currentCompany', 0);

		$this->smarty->assign('doNotShowControls', true);
		//	TODO: internal js script left there
		//$smarty->display("tpls:bulkUploader.tpl");
		$jsSources = array("modules/js/checkBoxes.js",
			"modules/js/reg_country_state.js");

		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl', 'tpls/bulkUploader.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionUpload() {
		$input = array(
			"maxNumber" => $this->getFromPost('maxNumber'),
			"threshold" => $this->getFromPost('threshold'),
			"update" => $this->getFromPost('update'),
			"companyID" => $this->getFromPost('companyID')
		);

		// for PFP
		if ($this->getFromPost('pfp') == 'Startpfp' && $input['size'] < 1024000) {
			$this->smarty->assign("isPFP", 'Startpfp');

			$input['inputFile'] = $_FILES['inputFile']['tmp_name'];
			$input['realFileName'] = basename($_FILES['inputFile']['name']);
			$validation = new validateCSV($this->db);
			$validation->validatePFP($input); // array from csv

			if ($validation->productsCorrect) {
				for ($j = 0; $j < count($validation->productsCorrect); $j++) {
					if (!$this->isVolumeRatio($validation->productsCorrect[$j][0])) {
						foreach ($validation->productsCorrect[$j] as $key => $product) {
							$validation->productsCorrect[$j][$key] = $this->convertOzRatioToVolume($product);
						}
						$validation->productsCorrect[$j] = $this->convertFromCumulativeQty($validation->productsCorrect[$j]);
					}
					$validation->productsCorrect[$j] = $this->calcRatioVolume($validation->productsCorrect[$j]);
				}
			}
			
			$bu = new bulkUploader4PFP($this->db, $input, $validation);

			$errorCnt = count($validation->productsError);
			$correctCnt = count($validation->productsCorrect);
			$total = $errorCnt + $correctCnt;
			$percent = round($errorCnt * 100 / ($correctCnt + $errorCnt), 2);
			//
			$errorLog = $validation->errorComments;
			$errorLog .= "	Percent of errors is " . $percent . "%. Threshold is " . $input['threshold'] . "%.\n";

			$validationLogFile = fopen(DIR_PATH_LOGS . "validation.log", "a");
			fwrite($validationLogFile, $errorLog);
			fclose($validationLogFile);

			$title = new Titles($this->smarty);
			$title->titleBulkUploadResults();

			$this->smarty->assign("categoryID", "tab_" . $this->getFromPost('categoryID'));
			$this->smarty->assign("productsError", $validation->productsError);
			$this->smarty->assign("errorCnt", $errorCnt);
			$this->smarty->assign("correctCnt", $correctCnt);
			$this->smarty->assign("total", $total);
			$this->smarty->assign("input", $input);
			$this->smarty->assign("insertedCnt", $bu->insertedCnt);
			$this->smarty->assign("updatedCnt", $bu->updatedCnt);
			$this->smarty->assign("validationResult", $validation->validationResult);
			$this->smarty->assign("actions", $bu->actions);
			$this->smarty->assign("parent", $this->parent_category);


			//$smarty->display('tpls:bulkUploader.tpl');
			$jsSources = array("modules/js/checkBoxes.js",
				"modules/js/reg_country_state.js");

			$this->smarty->assign('jsSources', $jsSources);
			$this->smarty->assign('tpl', "tpls/uploadResults.tpl");
			$this->smarty->display("tpls:index.tpl");
		} else if ($this->getFromPost('gom') == 'StartGOM' && $input['size'] < 1024000) {
			// for GOM library
			$input['inputFile'] = $_FILES['inputFile']['tmp_name'];
			$input['realFileName'] = basename($_FILES['inputFile']['name']);
			$validation = new validateCSV($this->db);
			$validation->validateGOM($input);
			//var_dump($validation->productsCorrect);die();
			$bu = new bulkUploader4GOM($this->db, $input, $validation);
			
			$errorCnt = count($validation->productsError);
			$correctCnt = count($validation->productsCorrect);
			$total = $errorCnt + $correctCnt;
			$percent = round($errorCnt * 100 / ($correctCnt + $errorCnt), 2);
			//
			$errorLog = $validation->errorComments;
			$errorLog .= "	Percent of errors is " . $percent . "%. Threshold is " . $input['threshold'] . "%.\n";

			$validationLogFile = fopen(DIR_PATH_LOGS . "validation.log", "a");
			fwrite($validationLogFile, $errorLog);
			fclose($validationLogFile);

			$title = new Titles($this->smarty);
			$title->titleBulkUploadResults();

			$this->smarty->assign("categoryID", "tab_" . $this->getFromPost('categoryID'));
			$this->smarty->assign("productsError", $validation->productsError);
			$this->smarty->assign("errorCnt", $errorCnt);
			$this->smarty->assign("correctCnt", $correctCnt);
			$this->smarty->assign("total", $total);
			$this->smarty->assign("input", $input);
			$this->smarty->assign("insertedCnt", $bu->insertedCnt);
			$this->smarty->assign("updatedCnt", $bu->updatedCnt);
			$this->smarty->assign("validationResult", $validation->validationResult);
			$this->smarty->assign("actions", $bu->actions);
			$this->smarty->assign("parent", $this->parent_category);

			$jsSources = array("modules/js/checkBoxes.js",
				"modules/js/reg_country_state.js");

			$this->smarty->assign('jsSources', $jsSources);
			$this->smarty->assign('tpl', "tpls/uploadResults.tpl");
			$this->smarty->display("tpls:index.tpl");
		} else {
			//for PRODUCT
			//we should check input file!
			if ($input['size'] < 1024000) {
				$input['inputFile'] = $_FILES['inputFile']['tmp_name'];
				$input['realFileName'] = basename($_FILES['inputFile']['name']);
				$bu = new bulkUploader($this->db, $input);

				$errorCnt = count($bu->productsError);
				$correctCnt = count($bu->productsCorrect);
				$total = $errorCnt + $correctCnt;

				$title = new Titles($this->smarty);
				$title->titleBulkUploadResults();

				$this->smarty->assign("categoryID", "tab_" . $this->getFromPost('categoryID'));
				$this->smarty->assign("productsError", $bu->productsError);
				$this->smarty->assign("errorCnt", $errorCnt);
				$this->smarty->assign("correctCnt", $correctCnt);
				$this->smarty->assign("total", $total);
				$this->smarty->assign("input", $input);
				$this->smarty->assign("insertedCnt", $bu->insertedCnt);
				$this->smarty->assign("updatedCnt", $bu->updatedCnt);
				$this->smarty->assign("validationResult", $bu->validationResult);
				$this->smarty->assign("actions", $bu->actions);
				$this->smarty->assign("parent", $this->parent_category);


				//$smarty->display('tpls:bulkUploader.tpl');
				$jsSources = array("modules/js/checkBoxes.js",
					"modules/js/reg_country_state.js");

				$this->smarty->assign('jsSources', $jsSources);
				$this->smarty->assign('tpl', "tpls/uploadResults.tpl");
				$this->smarty->display("tpls:index.tpl");
			}
		}
	}

	// GET RATIO FOR PRODUCTS IN PFP
	private function rate($ar) {

		if (count($ar) > 1) {
			$first = array_shift($ar);

			$result[0] = $first * 100;
			foreach ($ar as $num) {
				$tmp = ($num ) * 100;

				$result[] = round($tmp);
			}
			$nod = $this->gcd_array($result);
			foreach ($result as $res) {
				$goodArr[] = $res / $nod;
			}

			return $goodArr;
		}
	}

	private function gcd($a, $b) {
		if ($a == 0 || $b == 0)
			return abs(max(abs($a), abs($b)));

		$r = $a % $b;
		return ($r != 0) ?
				$this->gcd($b, $r) :
				abs($b);
	}

	private function gcd_array($array, $a = 0) {
		$b = array_pop($array);
		return ($b === null) ?
				(int) $a :
				$this->gcd_array($array, $this->gcd($a, $b));
	}


	private function calcRatioVolume($products) {
		$ratioFieldIndex = bulkUploader4PFP::PRODUCTRATIO_INDEX;

		//	save ratio of base product
		$firstNum = $products[0][$ratioFieldIndex];
		$quan = array($firstNum);

		for ($i = 1; $i < count($products); $i++) {
			if ($products[$i][$ratioFieldIndex] != null) { // no ratio but product quantity exist
				$reverse = strrev($products[$i][$ratioFieldIndex]);
				if ($reverse[0] == "%") {

					$ranges = array();
					$ratioRangeTo = 0;

					if(bulkUploader4PFP::isRangeRatio($products[$i][$ratioFieldIndex])) {
						$ranges = bulkUploader4PFP::splitRangeRatio($products[$i][$ratioFieldIndex]);
						$number = $ranges[0];
					} else {
						$number = substr($products[$i][$ratioFieldIndex], 0, -1);
						$ranges = array($number);
					}

					//$number = substr($products[$i][$ratioFieldIndex], 0, -1);
					$number = $firstNum * $number / 100;
					$products[$i][bulkUploader4PFP::PRODUCTRATIO_INDEX] = $number;

					if(isset($ranges[1])) {
						$ratioRangeTo = $firstNum * $ranges[1] / 100;
						$products[$i]['ratioRangeFrom'] = $number;
						$products[$i]['ratioRangeTo'] = $ratioRangeTo;

						$products[$i]['ratioRangeFromOriginal'] = $ranges[0];
						$products[$i]['ratioRangeToOriginal'] = $ranges[1];
					}
				}
				$quan[] = $products[$i][$ratioFieldIndex];
			}
		}

		if ($quan) {
			$lcm = $this->rate($quan); //make ratio

			for ($i = 0; $i < count($products); $i++) {

				if(isset($products[$i]['ratioRangeTo'])) {
					$products[$i]['ratioRangeFrom'] = $lcm[$i];
					/*
					 * We need to calculate ratio for TO range value
					 * according to this
					 * $lcm[$i] <------> $products[$i][$ratioFieldIndex]
					 * X		<------> $products[$i]['ratioRangeTo']
					 *
					 * X = ($lcm[$i] * $products[$i]['ratioRangeTo']) / $products[$i][$ratioFieldIndex]
					 */
					$products[$i]['ratioRangeTo'] = ($lcm[$i] * $products[$i]['ratioRangeTo'])/$products[$i][$ratioFieldIndex];
				}
				$products[$i][$ratioFieldIndex] = $lcm[$i];
			}
		}

		return $products;
	}


	private function isVolumeRatio($product) {
		return (strtoupper($product[bulkUploader4PFP::PRODUCTUNITTYPE_INDEX]) == 'VOL') ? true : false;
	}


	private function convertOzRatioToVolume($product) {

		$unitTypeConverter = new UnitTypeConverter();

		$productObj = new Product($this->db);
		$productID = $productObj->getProductIdByName($product[bulkUploader4PFP::PRODUCTNR_INDEX]);
		if(!$productID) {
			throw new Exception('This is no product in database'.$product[bulkUploader4PFP::PRODUCTNR_INDEX]);
		}
		$productObj->initializeByID($productID);

		$density = new Density($this->db, $productObj->getDensityUnitID());
		if (!$density->getNumerator()) {
			throw new Exception("Failed to load Density with id ".$productObj->getDensityUnitID());
		}
		$densityType = array(
			'numerator'	=> $density->getNumerator(),
			'denominator'	=> $density->getDenominator()
		);

		$volumeQty = $unitTypeConverter->convertToDefault($product[bulkUploader4PFP::PRODUCTRATIO_INDEX], 'oz',
				$productObj->getDensity(), $densityType);

		$product[bulkUploader4PFP::PRODUCTRATIO_INDEX] = $volumeQty;
		$product[bulkUploader4PFP::PRODUCTUNITTYPE_INDEX] = 'VOL-CUMULATIVE';

		return $product;
	}


	private function convertFromCumulativeQty($products) {
		$productsCount = count($products);

		for($i = 0; $i < $productsCount; $i++ ) {
			if ($i > 0) {
				$cumulativeQty = $products[$i][bulkUploader4PFP::PRODUCTRATIO_INDEX] - $products[$i-1][bulkUploader4PFP::PRODUCTRATIO_INDEX];
				$products[$i][bulkUploader4PFP::PRODUCTRATIO_INDEX] = $cumulativeQty;
			}
			$products[$i][bulkUploader4PFP::PRODUCTUNITTYPE_INDEX] = 'VOL';

		}

		return $products;
	}

}

?>