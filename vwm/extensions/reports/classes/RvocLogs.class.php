<?php

class RvocLogs extends ReportCreator implements iReportCreator {

	private $dateBegin;
	private $dateEnd;
	private $rule;
	private $data;

	private $dateFormat;

	function RvocLogs($db, $reportRequest = null) {
		$this->db = $db;
		if (!is_null($reportRequest)) {
			$this->categoryType = $reportRequest->getCategoryType();
			$this->categoryID = $reportRequest->getCategoryID();
			$this->dateBegin = $reportRequest->getDateBegin();
			$this->dateEnd = $reportRequest->getDateEnd();
			$extraVar = $reportRequest->getExtraVar();
			$this->rule = $extraVar['rule'];
			$this->data = $extraVar['data'];
			$this->dateFormat = $reportRequest->getDateFormat();
		}
	}

	public function getReportRequestByGetVars($companyID) {
		//at first lets get data already filtered
		$categoryType = $_REQUEST['categoryLevel'];
		$id = $_REQUEST['id'];
		$reportType = $_REQUEST['reportType'];
		$format = $_REQUEST['format'];


		//and get them too
		$dateBegin = new TypeChain($_GET['date_begin'],'date',$this->db,$companyID,'company');
	    $dateEnd = new TypeChain($_GET['date_end'],'date',$this->db,$companyID,'company');

		$extraVar['rule'] = $_REQUEST['logs'];

		$data['responsiblePerson'] = (($_REQUEST['responsiblePerson'] == "[Responsible Person]") ? "" : $_REQUEST['responsiblePerson']);
		$data['title'] = (($_REQUEST['title'] == "[Title]") ? "" : $_REQUEST['title']);
		$data['notes'] = (($_REQUEST['notes'] == "[Notes]") ? "" : $_REQUEST['notes']);
        $data['spentTime'] = (isset($_REQUEST['spentTime']) ? true : false);
        $data['totalCost'] = (isset($_REQUEST['totalCost']) ? true : false);
		$extraVar['data'] = $data;
        
		//lets set extra vars in case its csv format
		if ($format == "csv") {
			$extraVar['commaSeparator'] = $_REQUEST['commaSeparator'];
			$extraVar['textDelimiter'] = $_REQUEST['textDelimiter'];
			if (strstr($extraVar['commaSeparator'],"\\")) {
				$extraVar['commaSeparator'] = substr(strstr($extraVar['commaSeparator'],"\\"),1);
			}
			if (strstr($extraVar['textDelimiter'],"\\")) {
				$extraVar['textDelimiter'] = str_replace("\\","",$extraVar['textDelimiter']);
			}
		}

		//finally: lets get	reportRequest object!
		$reportRequest = new ReportRequest($reportType, $categoryType, $id, $frequency, $format, $dateBegin, $dateEnd, $extraVar, $_SESSION['user_id']);
		return $reportRequest;
	}

	public function buildXML($fileName) {

		$debug = new Debug();
		//$debug->printMicrotime(__LINE__,__FILE__);
		$reportData = $this->data;
		//	get rule name
		$ruleObj = new Rule($this->db);
		//$debug->printMicrotime(__LINE__,__FILE__);
		$ruleDetails = $ruleObj->getRuleDetails($this->rule, true);
		//$debug->printMicrotime(__LINE__,__FILE__);
		$rule = $ruleDetails['rule_nr'];

		$facilityList = false;

		switch ($this->categoryType) {

			case "company":
				$facility = new Facility($this->db);
				$facilityList = $facility->getFacilityListByCompany($this->categoryID);
				foreach ($facilityList as $value) {
					$facilityString .= $value['id']. ",";
				}
				$facilityString = substr($facilityString,0,-1);

				$query = "SELECT e.equipment_id, e.equip_desc, e.permit, f.epa, f.facility_id " .
					"FROM mix m, department d, equipment e, facility f " .
					"WHERE m.department_id = d.department_id " .
					"AND m.equipment_id = e.equipment_id " .
					"AND d.facility_id = f.facility_id " .
					"AND m.rule_id = " . $this->rule . " " .
					"AND d.facility_id in (" . $facilityString . ")  " .
					"GROUP BY e.equip_desc, e.permit, f.epa";

				$company = new Company($this->db);
				$orgDetails = $company->getCompanyDetails($this->categoryID);
				$orgDetails["type"] = "company";
				break;

			case "facility":
				$query = "SELECT e.equipment_id, e.equip_desc, e.permit, e.expire, f.epa, f.facility_id " .
					"FROM mix m, department d,  equipment e, facility f " .
					"WHERE m.department_id = d.department_id " .
					"AND m.equipment_id = e.equipment_id " .
					"AND d.facility_id = f.facility_id " .
					"AND m.rule_id = " . $this->rule . " " .
					"AND d.facility_id = " . $this->categoryID . " " .
					"GROUP BY e.equip_desc, e.permit, f.epa";

				$facility = new Facility($this->db);
				$orgDetails = $facility->getFacilityDetails($this->categoryID);
				$company = new Company($this->db);
				$companyDetails = $company->getCompanyDetails($orgDetails['company_id']);
				$orgDetails["type"] = "facility";
				$orgDetails["voc_unittype_id"] = $companyDetails["voc_unittype_id"];
				break;

			case "department":
				$query = "SELECT e.equipment_id, e.equip_desc, e.permit, f.epa, f.facility_id  " .
					"FROM mix m, equipment e, department d, facility f " .
					"WHERE m.equipment_id = e.equipment_id " .
					"AND m.department_id = d.department_id " .
					"AND d.facility_id = f.facility_id " .
					"AND m.rule_id = " . $this->rule . " " .
					"AND m.department_id = " . $this->categoryID . " " .
					"GROUP BY e.equip_desc, e.permit, f.epa";



				$department = new Department($this->db);
				$departmentDetails = $department -> getDepartmentDetails($this->categoryID);
				$orgDetails["dep"] = $departmentDetails;
				$facility = new Facility($this->db);
				$orgDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
				$company = new Company($this->db);
				$companyDetails = $company->getCompanyDetails($orgDetails['company_id']);
				$orgDetails["type"] = "facility";
				$orgDetails["voc_unittype_id"] = $companyDetails["voc_unittype_id"];
				break;
		}

		//$debug->printMicrotime(__LINE__,__FILE__);
		$in = $this->group($query, $this->dateBegin, $this->dateEnd, $this->rule);
		//$debug->printMicrotime(__LINE__,__FILE__);

		//xml generation
		$this->createXML($orgDetails, $rule, $in['equipments'], $in['days'], $fileName, $reportData, $facilityList);
		//$debug->printMicrotime(__LINE__,__FILE__);
	}

	private function createXML($orgDetails, $rule, $equipments, $days, $fileName, $reportData, $facilityList = false) {

		$doc = new DOMDocument();
		$timeInterval = $doc->formatOutput = true;

		$pageTag = $doc->createElement( "page" );
		$doc->appendChild( $pageTag );

		$pageOrientation = $doc->createAttribute("orientation");
		$pageOrientation->appendChild(
			$doc->createTextNode("l")
		);
		$pageTag->appendChild($pageOrientation);

		$pageTopMargin = $doc->createAttribute("topmargin");
		$pageTopMargin->appendChild(
			$doc->createTextNode("5")
		);
		$pageTag->appendChild($pageTopMargin);

		$pageLeftMargin = $doc->createAttribute("leftmargin");
		$pageLeftMargin->appendChild(
			$doc->createTextNode("10")
		);
		$pageTag->appendChild($pageLeftMargin);

		$pageRightMargin = $doc->createAttribute("rightmargin");
		$pageRightMargin->appendChild(
			$doc->createTextNode("10")
		);
		$pageTag->appendChild($pageRightMargin);

		$metaTag = $doc->createElement( "meta" );
		$pageTag->appendChild( $metaTag );

		$metaName = $doc->createAttribute("name");
		$metaName->appendChild(
			$doc->createTextNode("basefont")
		);
		$metaTag->appendChild($metaName);

		$metaValue = $doc->createAttribute("value");
		$metaValue->appendChild(
			$doc->createTextNode("times")
		);
		$metaTag->appendChild($metaValue);


		$titleTag = $doc->createElement( "title" );
		$titleTag->appendChild(
			$doc->createTextNode("Daily Emissions Report")
		);
		$pageTag->appendChild( $titleTag );

		//$dateBegin = new TypeChain($_GET['date_begin'],'date',$this->db,$companyID,'company');
	    //$dateEnd = new TypeChain($_GET['date_end'],'date',$this->db,$companyID,'company');

		$periodTag = $doc->createElement( "period" );
		$periodTag->appendChild(
			//$doc->createTextNode("PERIOD: " . date('m.d.Y',min($days)) . " TO " . date('m.d.Y',max($days)) )
			$doc->createTextNode("PERIOD: " . $this->dateBegin . " TO " . $this->dateEnd )
		);
		$pageTag->appendChild( $periodTag );

		$title2Tag = $doc->createElement( "title2" );
		$title2Tag->appendChild(
			$doc->createTextNode("Coating and Solvent Usage")
		);
		$pageTag->appendChild( $title2Tag );

		$orgTag = $doc->createElement($orgDetails["type"]);
		$pageTag->appendChild( $orgTag );

		$orgNameTag = $doc->createElement( $orgDetails["type"] . "Name" );
		$orgNameTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgDetails["name"]))
		);
		$orgTag -> appendChild( $orgNameTag );

		$orgAddressTag = $doc->createElement($orgDetails["type"] . "Address" );
		$orgAddressTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgDetails["address"]))
		);
		$orgTag->appendChild( $orgAddressTag );

		$orgCityTag = $doc->createElement($orgDetails["type"] . "City" );
		$orgCityTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgDetails["city"].", ".$orgDetails["state"]. ", ".$orgDetails["zip"]))
		);
		$orgTag->appendChild( $orgCityTag );

		$orgCountyTag = $doc->createElement($orgDetails["type"] . "County" );
		$orgCountyTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgDetails["county"]))
		);
		$orgTag->appendChild( $orgCountyTag );

		$orgPhoneTag = $doc->createElement($orgDetails["type"] . "Phone" );
		$orgPhoneTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgDetails["phone"]))
		);
		$orgTag->appendChild( $orgPhoneTag );

		$orgFaxTag = $doc->createElement($orgDetails["type"] . "Fax" );
		$orgFaxTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgDetails["fax"]))
		);
		$orgTag->appendChild( $orgFaxTag );

		$ruleTag = $doc->createElement("rule" );
		$ruleTag->appendChild(
			$doc->createTextNode( html_entity_decode ($rule))
		);
		$pageTag->appendChild( $ruleTag );

		$gcg = new GCG($this->db);
		$gcgTag = $doc->createElement("gcg" );
		$gcgTag->appendChild(
			$doc->createTextNode( html_entity_decode ($gcg->getByID($orgDetails["gcg_id"])))
		);
		$pageTag->appendChild( $gcgTag );

		$notesTag = $doc->createElement("notes");
		$notesTag->appendChild(
			$doc->createTextNode( html_entity_decode ($reportData['notes']))
		);
		$pageTag->appendChild( $notesTag );

		$responsiblePersonTag = $doc->createElement("responsiblePerson");
		$responsiblePersonTag->appendChild(
			$doc->createTextNode( html_entity_decode ($reportData['responsiblePerson']))
		);
		$pageTag->appendChild( $responsiblePersonTag );

		$titleManualTag = $doc->createElement("titleManual");
		$titleManualTag->appendChild(
			$doc->createTextNode( html_entity_decode ($reportData['title']))
		);
		$pageTag->appendChild( $titleManualTag );

		$unittype = new Unittype($this->db);
		$quantityUnittypeTag = $doc->createElement("quantityUnittype");
		if (REGION == 'eu_uk') {
			$unitTypeConverter = new UnitTypeConverter("cm3");
			$quantityUnittypeTag->appendChild(
				$doc->createTextNode( 'cm3' )
			);
		} else {
			$unitTypeConverter = new UnitTypeConverter("us gallon");
			$quantityUnittypeTag->appendChild(
				$doc->createTextNode( 'gal' )
			);
		}
		$pageTag->appendChild( $quantityUnittypeTag );

		$vocUnittypeTag = $doc->createElement("vocUnittype");
		$vocUnittypeTag->appendChild(
			$doc->createTextNode( html_entity_decode ($unittype->getNameByID($orgDetails['voc_unittype_id'])))
		);
		$pageTag->appendChild( $vocUnittypeTag );

		$mixObj = new Mix($this->db);
		foreach ($equipments as $equipment) {
			$summaryEquipmentQty = 0;
			$summaryEquipmentVoc3 = 0;
			$summaryEquipmentTotalVoc = 0;
			$summaryEquipmentTotalSpentTime = 0;
			$summaryEquipmentTotalCost = 0;

			$equipmentTag = $doc->createElement( "equipment" );

			$equipmentName = $doc->createAttribute("name");
			$equipmentName->appendChild(
				$doc->createTextNode( html_entity_decode ($equipment['name']))
			);
			$equipmentTag->appendChild($equipmentName);

			$equipPermit = $doc->createAttribute("permitNo");
			$equipPermit->appendChild(
				$doc->createTextNode( html_entity_decode ($equipment['permit']))
			);
			$equipmentTag->appendChild($equipPermit);


			$facilityIdTag = $doc->createAttribute("facilityID" );
			$facilityIdTag->appendChild(
				$doc->createTextNode( html_entity_decode ($equipment['epa']))
			);
			$equipmentTag->appendChild( $facilityIdTag );

			foreach ($days as $day) {

				$dateTag = $doc->createElement( "date" );

				$dateDay = $doc->createAttribute("day");
				$dateDay->appendChild(
					$doc->createTextNode( date($this->dateFormat,$day) )
				);
				$dateTag->appendChild($dateDay);
				$cnt = 0;
				$totalQty = 0;
				$totalVoc3 = 0;
				$totalVoc = 0;
				$totalTime = 0;
				$totalCost = 0;
				
				
				$totalMixCost = 0;

				$dayTmp = date($this->dateFormat, $day);
				if(isset($equipment['mixes'][$dayTmp]))
				{
					/*Equipment существует*/

					$mixesByDay = $equipment['mixes'][$dayTmp];
					$qtyRatio = array();
					$vocwx = array();
					$sumQty = 0;
					$voc = 0;
					$mixRatio = "";
					$coatAsApplied = 0;
					$ratioSum = 0;
					$isToday = true;//раньше было false (!)
					$mixSpentTime = 0;

					foreach($mixesByDay as $mix)
					{
						//get mix total spent time
						$mixSpentTime = 0;
						$mixSpentTime  += $mix->spent_time;
						//get totalmix cost
						$stepInstanceId = $mix->step_id;
						if(!is_null($stepInstanceId)){
							$stepInstance = new VWM\Apps\Process\StepInstance($this->db);
							$stepInstance->setId($stepInstanceId);
							$stepInstance->load();
							$resourcesInstance = $stepInstance->getResources();
							foreach($resourcesInstance as $resourceInstance){
								$totalMixCost += $resourceInstance->getTotalCost();
							}
						}
						$totalMixCost += $mix->getMixPrice();
						
						//get pfp proprietary for understanding if we need display current quantity
						$isProprietary = 0;
						$pfpId = $mix->getPfpId();
						if(!is_null($pfpId)){
							$pfp = new \VWM\Apps\WorkOrder\Entity\Pfp();
							$pfp->setId($pfpId);
							$pfp->load();
							$isProprietary = $pfp->getIsProprietary();
							
						}
						
						//$mix['creationTime'] = str_replace('-','/',$mix['creationTime']);
						foreach ($mix->products as $product) {
							
								$cnt++;
								$productTag = $doc->createElement("product" );

								$supplierTag = $doc->createElement("supplier" );
								$supplierTag->appendChild(
									$doc->createTextNode( html_entity_decode ($product->supplier))
								);
								$productTag->appendChild( $supplierTag );

								$product_nrTag = $doc->createElement("productNo" );
								$product_nrTag->appendChild(
									$doc->createTextNode( html_entity_decode ($product->product_nr))
								);
								$productTag->appendChild( $product_nrTag );

								$product_nameTag = $doc->createElement("coatingSingle" );
								$product_nameTag->appendChild(
									$doc->createTextNode( html_entity_decode ($product->name." ".$product->coatDesc))
								);
								$productTag->appendChild( $product_nameTag );

								$voclxTag = $doc->createElement("vocOfMaterial" );
								$voclxTag->appendChild(
									$doc->createTextNode($product->voclx)
								);
								$productTag->appendChild( $voclxTag );

								$vocwxTag = $doc->createElement("voc2" );
								$vocwxTag->appendChild(
									$doc->createTextNode($product->vocwx)
								);
								$productTag->appendChild( $vocwxTag );

								$quantityTag = $doc->createElement("qtyUsed" );

								//$unitypeDetails = $unittype->getUnittypeDetails($product['unittype']);
								$densityObj = new Density($this->db, $product->density_unit_id);
								$densityType = array('numerator'=>$densityObj->getNumerator(), 'denominator'=>$densityObj->getDenominator());
								$qty = $unitTypeConverter->convertToDefault($product->quantity, $product->unittypeDetails['description'], $product->density, $densityType);
								$qty = round($qty,2);

								$sumQty += $qty;
								$qtyRatio[]= $qty*100;
								$vocwx[]= $product->vocwx;

								// not dispaly quantity if pfp is proprietary
								if ($isProprietary == 1) {
									$qty = 'IP';	
								}
						
								$quantityTag->appendChild(
											$doc->createTextNode($qty)
									);
								
								$productTag->appendChild( $quantityTag );
								$voc = $mix->voc;					//	move down
								$exemptRule = $mix->exempt_rule;	//	move down

								$dateTag->appendChild( $productTag );
						}
						
						if ($mix->isPfp){
							foreach ($mix->products as $product) {
								$mixRatioNotCalc .= $product->ratio_to_save.":";
							}
						}
						$lastDelimiter = strrpos($mixRatioNotCalc, ":");
						$mixRatioNotCalc = substr_replace($mixRatioNotCalc, '', $lastDelimiter);
						//var_dump($mixRatioTMP); die();

						if ($cnt != 0) {

							if ($isToday) {
								$lcm = $this->lcm_nums($qtyRatio);

								for($j=0;$j < count($qtyRatio);$j++) {

									$mixRatio .= $lcm/$qtyRatio[$j] . ":";
									$coatAsApplied += $vocwx[$j]*($lcm/$qtyRatio[$j]);
									$ratioSum += $lcm/$qtyRatio[$j];
								}
								$mixRatio = substr($mixRatio,0,-1);
								$coatAsApplied = $coatAsApplied/$ratioSum;

								$totalOnProjectTag = $doc->createElement("totalOnProject" );
								$labelAttr = $doc->createAttribute("label" );
								$labelAttr->appendChild(
									$doc->createTextNode("Total Used on Project# ". html_entity_decode ($mix->description))
								);
								$totalOnProjectTag->appendChild( $labelAttr );

								$mixRatioAttr = $doc->createAttribute("mixRatio" );
								$mixRatioAttr->appendChild(
									$doc->createTextNode($mixRatioNotCalc)
								);
								$totalOnProjectTag->appendChild( $mixRatioAttr );

								$qtyAttr = $doc->createAttribute("qty" );
								$qtyAttr->appendChild(
									$doc->createTextNode($sumQty)
								);
								$totalOnProjectTag->appendChild( $qtyAttr );
								$totalQty += $sumQty;

								$voc3Attr = $doc->createAttribute("voc3" );
								$voc3Attr->appendChild(
									$doc->createTextNode(round($coatAsApplied,2))
								);
								$totalOnProjectTag->appendChild( $voc3Attr );
								$totalVoc3 += round($coatAsApplied,2);

								$exemptAttr = $doc->createAttribute("exempt" );
								$exemptAttr->appendChild(
									$doc->createTextNode( html_entity_decode ($exemptRule))
								);
								$totalOnProjectTag->appendChild( $exemptAttr );

								$totalVocAttr = $doc->createAttribute("totalVoc" );
								$totalVocAttr->appendChild(
									$doc->createTextNode($voc)
								);
								$totalOnProjectTag->appendChild( $totalVocAttr );
								$totalVoc += $voc;
								
								//spent Time
								$spentTimeAttr = $doc->createAttribute("spentTime" );
								$spentTimeAttr->appendChild(
									$doc->createTextNode($mixSpentTime)
								);
								$totalOnProjectTag->appendChild( $spentTimeAttr );
								$totalTime += $mixSpentTime;
								
								//mix cost
								$mixCostAttr = $doc->createAttribute("mixCost" );
								$mixCostAttr->appendChild(
									$doc->createTextNode($totalMixCost)
								);
								$totalOnProjectTag->appendChild( $mixCostAttr );
								$totalCost += $totalMixCost;
								
								$dateTag->appendChild( $totalOnProjectTag );
							}
						}
						$mixRatioNotCalc = '';
					}
				}

				if ($cnt == 0) {
					$productTag = $doc->createElement("product" );

					$supplierTag = $doc->createElement("supplier" );
					$supplierTag->appendChild(
						$doc->createTextNode("N/A")
					);
					$productTag->appendChild( $supplierTag );

					$product_nrTag = $doc->createElement("productNo" );
					$product_nrTag->appendChild(
						$doc->createTextNode("N/A")
					);
					$productTag->appendChild( $product_nrTag );

					$product_nameTag = $doc->createElement("coatingSingle" );
					$product_nameTag->appendChild(
						$doc->createTextNode("none")
					);
					$productTag->appendChild( $product_nameTag );

					$voclxTag = $doc->createElement("vocOfMaterial" );
					$voclxTag->appendChild(
						$doc->createTextNode("0.00")
					);
					$productTag->appendChild( $voclxTag );

					$vocwxTag = $doc->createElement("voc2" );
					$vocwxTag->appendChild(
						$doc->createTextNode("0.00")
					);
					$productTag->appendChild( $vocwxTag );

					$quantityTag = $doc->createElement("qtyUsed" );
					$quantityTag->appendChild(
						$doc->createTextNode("0.00")
					);
					$productTag->appendChild( $quantityTag );

					$dateTag->appendChild( $productTag );


					$totalOnProjectTag = $doc->createElement("totalOnProject" );

					$labelAttr = $doc->createAttribute("label" );
					$labelAttr->appendChild(
						$doc->createTextNode("Total Used on Project#")
					);
					$totalOnProjectTag->appendChild( $labelAttr );

					$mixRatioAttr = $doc->createAttribute("mixRatio" );
					$mixRatioAttr->appendChild(
						$doc->createTextNode(" ")
					);
					$totalOnProjectTag->appendChild( $mixRatioAttr );

					$qtyAttr = $doc->createAttribute("qty" );
					$qtyAttr->appendChild(
						$doc->createTextNode("0.00")
					);
					$totalOnProjectTag->appendChild( $qtyAttr );
					//$totalQty += $sumQty;
					$totalQty += 0;

					$voc3Attr = $doc->createAttribute("voc3" );
					$voc3Attr->appendChild(
						$doc->createTextNode("0.00")
					);
					$totalOnProjectTag->appendChild( $voc3Attr );
					//$totalVoc3 += $coatAsApplied;
					$totalVoc3 += 0;

					$totalVocAttr = $doc->createAttribute("totalVoc" );
					$totalVocAttr->appendChild(
						$doc->createTextNode("0.00")
					);
					$totalOnProjectTag->appendChild( $totalVocAttr );

					$dateTag->appendChild( $totalOnProjectTag );
				}

				$totalLabelTag = $doc->createElement("totalLabel" );
				$totalLabelTag->appendChild(
					$doc->createTextNode("Daily total from " . html_entity_decode ( $equipment['name']))
				);
				$dateTag->appendChild( $totalLabelTag );

				$totalQtyTag = $doc->createElement("totalQty" );
				$totalQtyTag->appendChild(
					$doc->createTextNode($totalQty)
				);
				$dateTag->appendChild( $totalQtyTag );

				$summaryEquipmentQty += $totalQty;

				$totalVoc3Tag = $doc->createElement("totalVoc3" );
				$totalVoc3Tag->appendChild(
					$doc->createTextNode($totalVoc3)
				);
				$dateTag->appendChild( $totalVoc3Tag );

				$summaryEquipmentVoc3 += $totalVoc3;

				$totalTotalVocTag = $doc->createElement("totalTotalVoc" );
				$totalTotalVocTag->appendChild(
					$doc->createTextNode($totalVoc)
				);
				$dateTag->appendChild( $totalTotalVocTag );
				
				$summaryEquipmentTotalVoc += $totalVoc;
				
				$totalSpentTimeTag = $doc->createElement("totalSpentTime" );
				$totalSpentTimeTag->appendChild(
					$doc->createTextNode($totalTime)
				);
				$dateTag->appendChild( $totalSpentTimeTag );
				
				$summaryEquipmentTotalSpentTime += $totalTime;
				
				$totalMixCostTag = $doc->createElement("totalMixCost" );
				$totalMixCostTag->appendChild(
					$doc->createTextNode($totalCost)
				);
				$dateTag->appendChild( $totalMixCostTag );
				
				$summaryEquipmentTotalCost += $totalCost;
				
				$equipmentTag -> appendChild($dateTag);
			}

			$summaryEquipmentTag = $doc->createElement("summaryEquipment" );

			$summaryEquipmentQtyTag = $doc->createElement("summaryEquipmentQty" );
			$summaryEquipmentQtyTag->appendChild(
				$doc->createTextNode($summaryEquipmentQty)
			);
			$summaryEquipmentTag->appendChild( $summaryEquipmentQtyTag );
			$summaryQty[]= $summaryEquipmentQty;

			$summaryEquipmentVoc3Tag = $doc->createElement("summaryEquipmentVoc3" );
			$summaryEquipmentVoc3Tag->appendChild(
				$doc->createTextNode($summaryEquipmentVoc3)
			);
			$summaryEquipmentTag->appendChild( $summaryEquipmentVoc3Tag );
			$summaryVoc3[] = $summaryEquipmentVoc3;

			$summaryEquipmenTotalVocTag = $doc->createElement("summaryEquipmentTotalVoc" );
			$summaryEquipmenTotalVocTag->appendChild(
				$doc->createTextNode($summaryEquipmentTotalVoc)
			);
			$summaryEquipmentTag->appendChild( $summaryEquipmenTotalVocTag );
			$summaryTotalVoc[] = $summaryEquipmentTotalVoc;

			//Mix spent Time
			$summaryEquipmentSpentTimeTag = $doc->createElement("summaryEquipmentSpentTime" );
			$summaryEquipmentSpentTimeTag->appendChild(
				$doc->createTextNode($summaryEquipmentTotalSpentTime)
			);
			$summaryEquipmentTag->appendChild( $summaryEquipmentSpentTimeTag );
			$summaryTime[]= $summaryEquipmentTotalSpentTime;
			
			//Mix total cost
			$summaryEquipmentTotalMixCostTag = $doc->createElement("summaryEquipmentTotalMixCostTag" );
			$summaryEquipmentTotalMixCostTag->appendChild(
				$doc->createTextNode($summaryEquipmentTotalCost)
			);
			$summaryEquipmentTag->appendChild( $summaryEquipmentTotalMixCostTag );
			$summaryCost[]= $summaryEquipmentTotalCost;
			
			$equipmentTag->appendChild( $summaryEquipmentTag );

			$pageTag -> appendChild($equipmentTag);
		}

		$facilitiesTotals = array();

		//summary tags here
		$summaryTag = $doc->createElement("summary" );
		for($i=0;$i<count($equipments);$i++) {
			//	did caclulated totals for this facility already?
			if (isset($facilitiesTotals[$equipments[$i] ['facility_id'] ] )) {
				//	yes, let's add data from this equipemnt to it
				$facilityTotal['qty'] = $facilitiesTotals[ $equipments[$i]['facility_id'] ] ['qty'];
				$facilityTotal['voc3'] = $facilitiesTotals[ $equipments[$i]['facility_id'] ] ['voc3'];
				$facilityTotal['totalVoc'] = $facilitiesTotals[ $equipments[$i]['facility_id'] ] ['totalVoc'];
				$facilityTotal['spentTime'] = $facilitiesTotals[ $equipments[$i]['facility_id'] ] ['spentTime'];
				$facilityTotal['mixCost'] = $facilitiesTotals[ $equipments[$i]['facility_id'] ] ['mixCost'];
			} else {
				//	no
				$facilityTotal = array (
					'qty'		=> 0,
					'voc3'	=> 0,
					'totalVoc'	=> 0,
					'spentTime' => 0,
					'mixCost' => 0
				);
			}

			$summaryTotalEquipmentTag = $doc->createElement("summaryTotalEquipment" );

				$summaryEquipPar = $doc->createAttribute("equipment" );
				$summaryEquipPar->appendChild(
					$doc->createTextNode( html_entity_decode ($equipments[$i]['name']))
				);
				$summaryTotalEquipmentTag->appendChild( $summaryEquipPar );

				$summaryQtyPar = $doc->createAttribute("qty" );
				$summaryQtyPar->appendChild(
					$doc->createTextNode($summaryQty[$i])
				);
				$summaryTotalEquipmentTag->appendChild( $summaryQtyPar );
				$facilityTotal ['qty'] += $summaryQty[$i];

				$summaryVoc3Par = $doc->createAttribute("voc3" );
				$summaryVoc3Par->appendChild(
					$doc->createTextNode($summaryVoc3[$i])
				);
				$summaryTotalEquipmentTag->appendChild( $summaryVoc3Par );
				$facilityTotal['voc3'] += $summaryVoc3[$i];

				$summaryTotalVocPar = $doc->createAttribute("totalVoc" );
				$summaryTotalVocPar->appendChild(
					$doc->createTextNode($summaryTotalVoc[$i])
				);
				$summaryTotalEquipmentTag->appendChild( $summaryTotalVocPar );
				$facilityTotal['totalVoc'] += $summaryTotalVoc[$i];
				
				//mix spent Time
				$summarySpentTimePar = $doc->createAttribute("spentTime" );
				$summarySpentTimePar->appendChild(
					$doc->createTextNode($summaryTime[$i])
				);
				$summaryTotalEquipmentTag->appendChild( $summarySpentTimePar );
				$facilityTotal ['spentTime'] += $summaryTime[$i];
				
				//mix total cost
				$summaryTotalMixCostPar = $doc->createAttribute("mixCost" );
				$summaryTotalMixCostPar->appendChild(
					$doc->createTextNode($summaryCost[$i])
				);
				$summaryTotalEquipmentTag->appendChild( $summaryTotalMixCostPar );
				$facilityTotal ['mixCost'] += $summaryCost[$i];
				
			$summaryTag->appendChild( $summaryTotalEquipmentTag );

			if ($facilityList) {
				$facilitiesTotals[ $equipments[$i]['facility_id'] ] = $facilityTotal;
			}
		}

		//	at company level we need facility based totals
		if ($facilityList) {
			foreach ($facilityList as $facility) {
				$summaryTotalFacilityTag = $doc->createElement("summaryTotalFacility" );

				$summaryFacilityPar = $doc->createAttribute("facility" );
				$summaryFacilityPar->appendChild(
					$doc->createTextNode( html_entity_decode ($facility['name']))
				);
				$summaryTotalFacilityTag->appendChild($summaryFacilityPar);

				$summaryQtyPar = $doc->createAttribute("qty" );
				$summaryQtyPar->appendChild(
					$doc->createTextNode($facilitiesTotals[ $facility['id']] ['qty'])
				);
				$summaryTotalFacilityTag->appendChild( $summaryQtyPar );

				$summaryVoc3Par = $doc->createAttribute("voc3" );
				$summaryVoc3Par->appendChild(
					$doc->createTextNode($facilitiesTotals[ $facility['id']] ['voc3'])
				);
				$summaryTotalFacilityTag->appendChild( $summaryVoc3Par );

				$summaryTotalVocPar = $doc->createAttribute("totalVoc" );
				$summaryTotalVocPar->appendChild(
					$doc->createTextNode($facilitiesTotals[ $facility['id']] ['totalVoc'])
				);
				$summaryTotalFacilityTag->appendChild( $summaryTotalVocPar );

				$summaryTag->appendChild( $summaryTotalFacilityTag );
			}
		}

		$summarySumTag = $doc->createElement("summarySum" );

			$summarySumQtyPar = $doc->createAttribute("qty" );
			$summarySumQtyPar->appendChild(
				$doc->createTextNode(array_sum($summaryQty))
			);
			$summarySumTag->appendChild( $summarySumQtyPar );

			$summarySumVoc3Par = $doc->createAttribute("voc3" );
			$summarySumVoc3Par->appendChild(
				$doc->createTextNode(array_sum($summaryVoc3))
			);
			$summarySumTag->appendChild( $summarySumVoc3Par );

			$summarySumTotalVocPar = $doc->createAttribute("totalVoc" );
			$summarySumTotalVocPar->appendChild(
				$doc->createTextNode(array_sum($summaryTotalVoc))
			);
			$summarySumTag->appendChild( $summarySumTotalVocPar );

			$summarySumTimePar = $doc->createAttribute("spentTime" );
			$summarySumTimePar->appendChild(
				$doc->createTextNode(array_sum($summaryTime))
			);
			$summarySumTag->appendChild( $summarySumTimePar );
			
			$summarySumCostPar = $doc->createAttribute("mixCost" );
			$summarySumCostPar->appendChild(
				$doc->createTextNode(array_sum($summaryCost))
			);
			$summarySumTag->appendChild( $summarySumCostPar );
			
		$summaryTag->appendChild( $summarySumTag );

		$pageTag->appendChild( $summaryTag );

		$doc->save($fileName);
	}

	private function group($query, $dateBegin, $dateEnd, $ruleID) {
		$this->db->query($query);

		if ($this->db->num_rows()) {
			$equipmentsData = $this->db->fetch_all();
			foreach ($equipmentsData as $equipmentData) {
				$equipment = array (
					'id'				=>	$equipmentData->equipment_id,
					'name'			=>	$equipmentData->equip_desc,
					'permit'			=>	$equipmentData->permit,
					'epa'				=>	$equipmentData->epa,
					'facility_id'			=>	$equipmentData->facility_id,
				);
				$query = "SELECT mix_id FROM ".TB_USAGE." WHERE equipment_id = ".$equipment['id']." AND rule_id = ".$ruleID;
				$this->db->query($query);

				if ($this->db->num_rows()) {

					$mixesData = $this->db->fetch_all();

					foreach ($mixesData as $mixData) {
						$mix = new MixOptimized($this->db, $mixData->mix_id);
						$creationTime = $mix->creation_time;

						if(!isset($equipment['mixes'][$creationTime])) {
							$equipment['mixes'][$creationTime] = array();
						}

						$equipment['mixes'][$creationTime][] = $mix;
					}

					$equipments[] = $equipment;
				}
			}

		}

		//	create day list
		$dateBeginObj = DateTime::createFromFormat($this->dateFormat, $this->dateBegin);
		$dateEndObj = DateTime::createFromFormat($this->dateFormat, $this->dateEnd);
		$days[0] = $dateBeginObj->getTimestamp();
		$i = 1;
		while ($days[$i-1] < $dateEndObj->getTimestamp()) {
			$days[$i] = $days[$i-1] + 86400;	//60*60*24 - seconds in one day
			$i++;
		}

		$out['equipments'] = $equipments;
		$out['days'] = $days;

		return $out;
	}

	private function gcm($a, $b) {
		return ( $b == 0 ) ? ($a):( $this->gcm($b, $a % $b) );
	}

	private function lcm($a, $b) {
		return ( $a / $this->gcm($a,$b) ) * $b;
	}

	private function lcm_nums($ar) {
		if (count($ar) > 1) {
			$ar[] = $this->lcm( array_shift($ar) , array_shift($ar) );
			return $this->lcm_nums( $ar );
		} else {
			return $ar[0];
		}
	}
}
?>