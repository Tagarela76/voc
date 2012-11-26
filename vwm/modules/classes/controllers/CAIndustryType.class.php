<?php

use VWM\Label\CompanyLabelManager;
use VWM\Label\CompanyLevelLabel;
use VWM\ManageColumns\BrowseCategoryEntity;
use VWM\ManageColumns\DisplayColumnsSettings;

class CAIndustryType extends Controller {
	
	function CAIndustryType($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='industryType';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	
	
	protected function actionBrowseCategory($vars) {			
		$this->bookmarkIndustryType($vars);
	}
	
	
	
	protected function bookmarkIndustryType($vars) { 
		extract($vars);

		$industryTypeManager = new IndustryTypeManager($this->db);
		
		// get industry types count
		if (!is_null($this->getFromRequest('q'))){
			$itemsCount = $industryTypeManager->searchTypeResultsCount($this->getFromRequest('q'));
		} else {
			$itemsCount = $industryTypeManager->getIndustryTypesCount();
		}
		// Pagination
		$url = "?".$_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/","", $url);
        $pagination = new Pagination($itemsCount);
		$pagination->url = $url; 
		$this->smarty->assign('pagination', $pagination);
		
		if (!is_null($this->getFromRequest('q'))){
			$allTypes = $industryTypeManager->searchType($this->getFromRequest('q'), $pagination);
		} else {
			$allTypes = $industryTypeManager->getIndustryTypes($pagination);
		}

		$i = 0;
		foreach ($allTypes as $item){
			$allTypes[$i]->url = 'admin.php?action=viewDetails&category=industryType&id='.$item->id;
			$i++;
		}

		$this->smarty->assign('itemsCount', $itemsCount);
		$this->smarty->assign('allTypes', $allTypes);
		$this->smarty->assign('tpl', 'tpls/industryTypeClass.tpl');
	}
	
	private function actionViewDetails() {
		
		$industryType = new IndustryType($this->db, $this->getFromRequest('id')); 
		$subIndustryTypes = $industryType->getSubIndustryTypes();
		// get industry type label list
		$companyLevelLabel = new CompanyLevelLabel($this->db);
        // for repair order
        $companyLevelLabelRepairOrderDefault = $companyLevelLabel->getRepairOrderLabel(); 
        $repairOrderLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelRepairOrderDefault->label_id)->getLabelText(); 
        // Mix Browse Category Labels
        // Product Name
        $companyLevelLabelProductNameDefault = $companyLevelLabel->getProductNameLabel();
        $productNameLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelProductNameDefault->label_id)->getLabelText(); 
        // Add Job
        $companyLevelLabelAddJobDefault = $companyLevelLabel->getAddJobLabel();
        $addJobLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelAddJobDefault->label_id)->getLabelText(); 
        // Description
        $companyLevelLabelDescriptionDefault = $companyLevelLabel->getDescriptionLabel();
        $descriptionLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelDescriptionDefault->label_id)->getLabelText();
        // R/O Description
        $companyLevelLabelRODescriptionDefault = $companyLevelLabel->getRODescriptionLabel();
        $roDescriptionLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelRODescriptionDefault->label_id)->getLabelText();
        // R/O VIN Number
        $companyLevelLabelROVinNumberDefault = $companyLevelLabel->getROVinNumberLabel();
        $roVinNumberLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelROVinNumberDefault->label_id)->getLabelText();
        // Contact
        $companyLevelLabelContactDefault = $companyLevelLabel->getContactLabel();
        $contactLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelContactDefault->label_id)->getLabelText();
        // Voc
        $companyLevelLabelVocDefault = $companyLevelLabel->getVocLabel();
        $vocLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelVocDefault->label_id)->getLabelText();
        // Creation Date
        $companyLevelLabelCreationDateDefault = $companyLevelLabel->getCreationDateLabel();
        $creationDateLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelCreationDateDefault->label_id)->getLabelText();
        // Unit Type
        $companyLevelLabelUnitTypeDefault = $companyLevelLabel->getUnitTypeLabel();
        $unitTypeLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelUnitTypeDefault->label_id)->getLabelText();
        
        // Paint Shop Product
        $companyLevelLabelPaintShopProductDefault = $companyLevelLabel->getPaintShopProductLabel();
        $paintShopProductLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelPaintShopProductDefault->label_id)->getLabelText();
        
        // Body Shop Product
        $companyLevelLabelBodyShopProductDefault = $companyLevelLabel->getBodyShopProductLabel();
        $bodyShopProductLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelBodyShopProductDefault->label_id)->getLabelText();
        
        // Detailing Shop Product
        $companyLevelLabelDetailingShopProductDefault = $companyLevelLabel->getDetailingShopProductLabel();
        $detailingShopProductLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelDetailingShopProductDefault->label_id)->getLabelText();
        
        // Fuel and Oils Label
        $companyLevelLabelFuelAndOilProductDefault = $companyLevelLabel->getFuelAndOilProductLabel(); 
        $fuelAndOilProductLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelFuelAndOilProductDefault->label_id)->getLabelText();
        
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);
        $this->smarty->assign('companyLevelLabelRepairOrderDefault', $companyLevelLabelRepairOrderDefault);
        
        $this->smarty->assign('productNameLabel', $productNameLabel);
        $this->smarty->assign('companyLevelLabelProductNameDefault', $companyLevelLabelProductNameDefault);
        
        $this->smarty->assign('addJobLabel', $addJobLabel);
        $this->smarty->assign('companyLevelLabelAddJobDefault', $companyLevelLabelAddJobDefault);
        
        $this->smarty->assign('descriptionLabel', $descriptionLabel);
        $this->smarty->assign('companyLevelLabelDescriptionDefault', $companyLevelLabelDescriptionDefault);
        
        $this->smarty->assign('roDescriptionLabel', $roDescriptionLabel);
        $this->smarty->assign('companyLevelLabelRODescriptionDefault', $companyLevelLabelRODescriptionDefault);
        
        $this->smarty->assign('contactLabel', $contactLabel);
        $this->smarty->assign('companyLevelLabelContactDefault', $companyLevelLabelContactDefault);
        
        $this->smarty->assign('roVinNumberLabel', $roVinNumberLabel);
        $this->smarty->assign('companyLevelLabelROVinNumberDefault', $companyLevelLabelROVinNumberDefault);
        
        $this->smarty->assign('creationDateLabel', $creationDateLabel);
        $this->smarty->assign('companyLevelLabelCreationDateDefault', $companyLevelLabelCreationDateDefault);
        
        $this->smarty->assign('vocLabel', $vocLabel);
        $this->smarty->assign('companyLevelLabelVocDefault', $companyLevelLabelVocDefault);
        
        $this->smarty->assign('unitTypeLabel', $unitTypeLabel);
        $this->smarty->assign('companyLevelLabelUnitTypeDefault', $companyLevelLabelUnitTypeDefault);
        
        $this->smarty->assign('paintShopProductLabel', $paintShopProductLabel);
        $this->smarty->assign('companyLevelLabelPaintShopProductDefault', $companyLevelLabelPaintShopProductDefault);
        
        $this->smarty->assign('bodyShopProductLabel', $bodyShopProductLabel);
        $this->smarty->assign('companyLevelLabelBodyShopProductDefault', $companyLevelLabelBodyShopProductDefault);
        
        $this->smarty->assign('detailingShopProductLabel', $detailingShopProductLabel);
        $this->smarty->assign('companyLevelLabelDetailingShopProductDefault', $companyLevelLabelDetailingShopProductDefault);
        
        $this->smarty->assign('fuelAndOilProductLabel', $fuelAndOilProductLabel);
        $this->smarty->assign('companyLevelLabelFuelAndOilProductDefault', $companyLevelLabelFuelAndOilProductDefault);
        
		// get browse category list
        $browseCategoryEntity = new BrowseCategoryEntity($this->db);
		$browseCategoryMix = $browseCategoryEntity->getBrowseCategoryMix(); 
        $columnsSettingsMixValue = $industryType->getDisplayColumnsManager()->getDisplayColumnsSettings($browseCategoryMix->name)->getValue();
        $columnsSettingsMixValueArray = explode(",", $columnsSettingsMixValue);

        $mixColumn4Display = array();
        foreach ($columnsSettingsMixValueArray as $columnId) {
            $mixColumn4Display[] = $industryType->getLabelManager()->getLabel($columnId)->getLabelText(); 
        }
        $columnsSettingsMixValue = implode(",", $mixColumn4Display);

		$this->smarty->assign('browseCategoryMix', $browseCategoryMix);
		$this->smarty->assign('columnsSettingsMixValue', $columnsSettingsMixValue);
		$this->smarty->assign('typeDetails', $industryType);
		$this->smarty->assign('subIndustryTypes', $subIndustryTypes);
		$this->smarty->assign('tpl', 'tpls/viewIndustryType.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {

		$industryType = new IndustryType($this->db, $this->getFromRequest('id')); 
		$post  = $this->getFromPost();
		// get industry type label list
		$companyLevelLabel = new CompanyLevelLabel($this->db);
        $companyLabelManager = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
        // for repair order
        $companyLevelLabelRepairOrderDefault = $companyLevelLabel->getRepairOrderLabel(); 
        $repairOrderLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelRepairOrderDefault->label_id)->getLabelText(); 
        // Mix Browse Category Labels
        // Product Name
        $companyLevelLabelProductNameDefault = $companyLevelLabel->getProductNameLabel();
        $productNameLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelProductNameDefault->label_id)->getLabelText(); 
        // Add Job
        $companyLevelLabelAddJobDefault = $companyLevelLabel->getAddJobLabel();
        $addJobLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelAddJobDefault->label_id)->getLabelText(); 
        // Description
        $companyLevelLabelDescriptionDefault = $companyLevelLabel->getDescriptionLabel();
        $descriptionLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelDescriptionDefault->label_id)->getLabelText();
        // R/O Description
        $companyLevelLabelRODescriptionDefault = $companyLevelLabel->getRODescriptionLabel();
        $roDescriptionLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelRODescriptionDefault->label_id)->getLabelText();
        // R/O VIN Number
        $companyLevelLabelROVinNumberDefault = $companyLevelLabel->getROVinNumberLabel();
        $roVinNumberLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelROVinNumberDefault->label_id)->getLabelText();
        // Contact
        $companyLevelLabelContactDefault = $companyLevelLabel->getContactLabel();
        $contactLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelContactDefault->label_id)->getLabelText();
        // Voc
        $companyLevelLabelVocDefault = $companyLevelLabel->getVocLabel();
        $vocLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelVocDefault->label_id)->getLabelText();
        // Creation Date
        $companyLevelLabelCreationDateDefault = $companyLevelLabel->getCreationDateLabel();
        $creationDateLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelCreationDateDefault->label_id)->getLabelText();
        // Unit Type
        $companyLevelLabelUnitTypeDefault = $companyLevelLabel->getUnitTypeLabel();
        $unitTypeLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelUnitTypeDefault->label_id)->getLabelText();
        
        // Paint Shop Product
        $companyLevelLabelPaintShopProductDefault = $companyLevelLabel->getPaintShopProductLabel();
        $paintShopProductLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelPaintShopProductDefault->label_id)->getLabelText();
        
        // Body Shop Product
        $companyLevelLabelBodyShopProductDefault = $companyLevelLabel->getBodyShopProductLabel();
        $bodyShopProductLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelBodyShopProductDefault->label_id)->getLabelText();
        
        // Detailing Shop Product
        $companyLevelLabelDetailingShopProductDefault = $companyLevelLabel->getDetailingShopProductLabel();
        $detailingShopProductLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelDetailingShopProductDefault->label_id)->getLabelText();
        
        // Fuel and Oils Label
        $companyLevelLabelFuelAndOilProductDefault = $companyLevelLabel->getFuelAndOilProductLabel();
        $fuelAndOilProductLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelFuelAndOilProductDefault->label_id)->getLabelText();
        
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);
        $this->smarty->assign('companyLevelLabelRepairOrderDefault', $companyLevelLabelRepairOrderDefault);
        
        $this->smarty->assign('productNameLabel', $productNameLabel);
        $this->smarty->assign('companyLevelLabelProductNameDefault', $companyLevelLabelProductNameDefault);
        
        $this->smarty->assign('addJobLabel', $addJobLabel);
        $this->smarty->assign('companyLevelLabelAddJobDefault', $companyLevelLabelAddJobDefault);
        
        $this->smarty->assign('descriptionLabel', $descriptionLabel);
        $this->smarty->assign('companyLevelLabelDescriptionDefault', $companyLevelLabelDescriptionDefault);
        
        $this->smarty->assign('roDescriptionLabel', $roDescriptionLabel);
        $this->smarty->assign('companyLevelLabelRODescriptionDefault', $companyLevelLabelRODescriptionDefault);
        
        $this->smarty->assign('contactLabel', $contactLabel);
        $this->smarty->assign('companyLevelLabelContactDefault', $companyLevelLabelContactDefault);
        
        $this->smarty->assign('roVinNumberLabel', $roVinNumberLabel);
        $this->smarty->assign('companyLevelLabelROVinNumberDefault', $companyLevelLabelROVinNumberDefault);
        
        $this->smarty->assign('creationDateLabel', $creationDateLabel);
        $this->smarty->assign('companyLevelLabelCreationDateDefault', $companyLevelLabelCreationDateDefault);
        
        $this->smarty->assign('vocLabel', $vocLabel);
        $this->smarty->assign('companyLevelLabelVocDefault', $companyLevelLabelVocDefault);

        $this->smarty->assign('unitTypeLabel', $unitTypeLabel);
        $this->smarty->assign('companyLevelLabelUnitTypeDefault', $companyLevelLabelUnitTypeDefault);
        
        $this->smarty->assign('paintShopProductLabel', $paintShopProductLabel);
        $this->smarty->assign('companyLevelLabelPaintShopProductDefault', $companyLevelLabelPaintShopProductDefault);
        
        $this->smarty->assign('bodyShopProductLabel', $bodyShopProductLabel);
        $this->smarty->assign('companyLevelLabelBodyShopProductDefault', $companyLevelLabelBodyShopProductDefault);
        
        $this->smarty->assign('detailingShopProductLabel', $detailingShopProductLabel);
        $this->smarty->assign('companyLevelLabelDetailingShopProductDefault', $companyLevelLabelDetailingShopProductDefault);
        
        $this->smarty->assign('fuelAndOilProductLabel', $fuelAndOilProductLabel);
        $this->smarty->assign('companyLevelLabelFuelAndOilProductDefault', $companyLevelLabelFuelAndOilProductDefault);
        
		// get browse category list
        $browseCategoryEntity = new BrowseCategoryEntity($this->db);
		$browseCategoryMix = $browseCategoryEntity->getBrowseCategoryMix(); 
        $columnsSettingsMixValue = $industryType->getDisplayColumnsManager()->getDisplayColumnsSettings($browseCategoryMix->name)->getValue();
        $columnsSettingsMixValueArray = explode(",", $columnsSettingsMixValue);

        $mixColumn4Display = array();
        foreach ($columnsSettingsMixValueArray as $columnId) {
            $mixColumn4Display[] = $industryType->getLabelManager()->getLabel($columnId)->getLabelText();
        }
        $columnsSettingsMixValue = implode(",", $mixColumn4Display);

		$this->smarty->assign('browseCategoryMix', $browseCategoryMix);
		$this->smarty->assign('columnsSettingsMixValue', $columnsSettingsMixValue);
		$this->smarty->assign('columnsSettingsMixValueArray', $columnsSettingsMixValueArray);

		//	set js scripts
		$jsSources = array(
			"modules/js/autocomplete/jquery.autocomplete.js",
			"modules/js/checkBoxes.js",
			"modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js",
            'modules/js/manageDisplayColumnsSettings.js'
		);
		$this->smarty->assign('jsSources', $jsSources);
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		
		if ($this->getFromPost('save') == 'Save') {
			// save display columns settings for mix entity
			$value = $this->getFromPost('browseCategoryMix_id'); 
			$columnsDisplayValue = implode(",", $value);
			// we should knew - insert/update. So i get columns settings and set display columns settings id
            $displayColumnsSettings = $industryType->getDisplayColumnsManager();
			$columnsSettingsMix = $displayColumnsSettings->getDisplayColumnsSettings($browseCategoryMix->name);
			if ($columnsSettingsMix) {
				// update
				$displayColumnsSettings->setId($columnsSettingsMix->getId());
			}
			$displayColumnsSettings->setBrowseCategoryEntityId($browseCategoryMix->id);
			$displayColumnsSettings->setValue($columnsDisplayValue); 
			$displayColumnsSettings->save();
				
			$industryType->type = $post["type"];
			$violationList = $industryType->validate(); 
			if(count($violationList) == 0) {		
				$industryType->save(); 
                $errors = $companyLabelManager->validate($post); 
				if ($errors["validateStatus"] == "false") {					
					$notifyc = new Notify(null, $this->db);
					$notify = $notifyc->getPopUpNotifyMessage(401);
					$this->smarty->assign("notify", $notify);						
					$this->smarty->assign('data', $industryType);
                    $this->smarty->assign('errors', $errors);
				} else {
					// save label
                    $companyLevelLabelRepairOrder = $industryType->getLabelManager()->getLabel($companyLevelLabelRepairOrderDefault->label_id);
                    $companyLevelLabelRepairOrder->setCompanyLevelLabelId($companyLevelLabelRepairOrderDefault->id);
                    $companyLevelLabelRepairOrder->setLabelText($post[$companyLevelLabelRepairOrderDefault->label_id]);
                    $companyLevelLabelRepairOrder->save();
                    
                    $companyLevelLabelProductName = $industryType->getLabelManager()->getLabel($companyLevelLabelProductNameDefault->label_id);
                    $companyLevelLabelProductName->setCompanyLevelLabelId($companyLevelLabelProductNameDefault->id);
                    $companyLevelLabelProductName->setLabelText($post[$companyLevelLabelProductNameDefault->label_id]);
                    $companyLevelLabelProductName->save();
                    
                    $companyLevelLabelAddJob = $industryType->getLabelManager()->getLabel($companyLevelLabelAddJobDefault->label_id);
                    $companyLevelLabelAddJob->setCompanyLevelLabelId($companyLevelLabelAddJobDefault->id);
                    $companyLevelLabelAddJob->setLabelText($post[$companyLevelLabelAddJobDefault->label_id]);
                    $companyLevelLabelAddJob->save();
                    
                    $companyLevelLabelDescription = $industryType->getLabelManager()->getLabel($companyLevelLabelDescriptionDefault->label_id);
                    $companyLevelLabelDescription->setCompanyLevelLabelId($companyLevelLabelDescriptionDefault->id);
                    $companyLevelLabelDescription->setLabelText($post[$companyLevelLabelDescriptionDefault->label_id]);
                    $companyLevelLabelDescription->save();
                 
                    $companyLevelLabelRODescription = $industryType->getLabelManager()->getLabel($companyLevelLabelRODescriptionDefault->label_id);
                    $companyLevelLabelRODescription->setCompanyLevelLabelId($companyLevelLabelRODescriptionDefault->id); 
                    $companyLevelLabelRODescription->setLabelText($post[$companyLevelLabelRODescriptionDefault->label_id]);
                    $companyLevelLabelRODescription->save();
                    
                    $companyLevelLabelROVinNumber = $industryType->getLabelManager()->getLabel($companyLevelLabelROVinNumberDefault->label_id);
                    $companyLevelLabelROVinNumber->setCompanyLevelLabelId($companyLevelLabelROVinNumberDefault->id);
                    $companyLevelLabelROVinNumber->setLabelText($post[$companyLevelLabelROVinNumberDefault->label_id]);
                    $companyLevelLabelROVinNumber->save();
                    
                    $companyLevelLabelContact = $industryType->getLabelManager()->getLabel($companyLevelLabelContactDefault->label_id);
                    $companyLevelLabelContact->setCompanyLevelLabelId($companyLevelLabelContactDefault->id); 
                    $companyLevelLabelContact->setLabelText($post[$companyLevelLabelContactDefault->label_id]);
                    $companyLevelLabelContact->save();
                    
                    $companyLevelLabelVoc = $industryType->getLabelManager()->getLabel($companyLevelLabelVocDefault->label_id);
                    $companyLevelLabelVoc->setCompanyLevelLabelId($companyLevelLabelVocDefault->id);
                    $companyLevelLabelVoc->setLabelText($post[$companyLevelLabelVocDefault->label_id]);
                    $companyLevelLabelVoc->save();
                    
                    $companyLevelLabelCreationDate = $industryType->getLabelManager()->getLabel($companyLevelLabelCreationDateDefault->label_id);
                    $companyLevelLabelCreationDate->setCompanyLevelLabelId($companyLevelLabelCreationDateDefault->id);
                    $companyLevelLabelCreationDate->setLabelText($post[$companyLevelLabelCreationDateDefault->label_id]);
                    $companyLevelLabelCreationDate->save();
                    
                    $companyLevelLabelUnitType = $industryType->getLabelManager()->getLabel($companyLevelLabelUnitTypeDefault->label_id);
                    $companyLevelLabelUnitType->setCompanyLevelLabelId($companyLevelLabelUnitTypeDefault->id);
                    $companyLevelLabelUnitType->setLabelText($post[$companyLevelLabelUnitTypeDefault->label_id]);
                    $companyLevelLabelUnitType->save();
                    
                    $companyLevelLabelPaintShopProduct = $industryType->getLabelManager()->getLabel($companyLevelLabelPaintShopProductDefault->label_id);
                    $companyLevelLabelPaintShopProduct->setCompanyLevelLabelId($companyLevelLabelPaintShopProductDefault->id);
                    $companyLevelLabelPaintShopProduct->setLabelText($post[$companyLevelLabelPaintShopProductDefault->label_id]);
                    $companyLevelLabelPaintShopProduct->save();
                    
                    $companyLevelLabelBodyShopProduct = $industryType->getLabelManager()->getLabel($companyLevelLabelBodyShopProductDefault->label_id);
                    $companyLevelLabelBodyShopProduct->setCompanyLevelLabelId($companyLevelLabelBodyShopProductDefault->id);
                    $companyLevelLabelBodyShopProduct->setLabelText($post[$companyLevelLabelBodyShopProductDefault->label_id]);
                    $companyLevelLabelBodyShopProduct->save();
                    
                    $companyLevelLabelDetailingShopProduct = $industryType->getLabelManager()->getLabel($companyLevelLabelDetailingShopProductDefault->label_id);
                    $companyLevelLabelDetailingShopProduct->setCompanyLevelLabelId($companyLevelLabelDetailingShopProductDefault->id);
                    $companyLevelLabelDetailingShopProduct->setLabelText($post[$companyLevelLabelDetailingShopProductDefault->label_id]);
                    $companyLevelLabelDetailingShopProduct->save();
                    
                    $companyLevelLabelFuelAndOilProduct = $industryType->getLabelManager()->getLabel($companyLevelLabelFuelAndOilProductDefault->label_id);
                    $companyLevelLabelFuelAndOilProduct->setCompanyLevelLabelId($companyLevelLabelFuelAndOilProductDefault->id);
                    $companyLevelLabelFuelAndOilProduct->setLabelText($post[$companyLevelLabelFuelAndOilProductDefault->label_id]);
                    $companyLevelLabelFuelAndOilProduct->save();
                    
					// redirect
					header("Location: ?action=viewDetails&category=industryType&id=" . $this->getFromRequest('id') . "&&notify=54");
				}
			} else {						
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);						
				$this->smarty->assign('violationList', $violationList);
				$this->smarty->assign('data', $post);
			}	
		} else {
            $this->smarty->assign('data', $industryType);
        }		
		$this->smarty->assign("currentOperation","edit");
		$this->smarty->assign('tpl','tpls/addIndustryTypeClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
        
        $industryType = new IndustryType($this->db);
        $post = $this->getFromPost();
		// get browse category list
		$browseCategoryEntity = new BrowseCategoryEntity($this->db);
		$browseCategoryMix = $browseCategoryEntity->getBrowseCategoryMix(); 
		$columnsSettingsMixValue = $browseCategoryMix->default_value;
		$columnsSettingsMixValueArray = explode(',', $columnsSettingsMixValue);
        $mixColumn4Display = array();
        foreach ($columnsSettingsMixValueArray as $columnId) {
            $mixColumn4Display[] = $industryType->getLabelManager()->getLabel($columnId)->getLabelText();
        }
        $columnsSettingsMixValue = implode(",", $mixColumn4Display);
		$this->smarty->assign('browseCategoryMix', $browseCategoryMix);
		$this->smarty->assign('columnsSettingsMixValue', $columnsSettingsMixValue);
		$this->smarty->assign('columnsSettingsMixValueArray', $columnsSettingsMixValueArray);

		//	set js scripts
		$jsSources = array(
			"modules/js/autocomplete/jquery.autocomplete.js",
			"modules/js/checkBoxes.js",
			"modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js",
            'modules/js/manageDisplayColumnsSettings.js'
		);
		$this->smarty->assign('jsSources', $jsSources);
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		
        if ($this->getFromPost('save') == 'Save'){

            $industryType->type = $post["type"];
            $industryType->setValidationGroup("add");
            $violationList = $industryType->validate(); 
            if(count($violationList) == 0) {		
                $industryTypeId = $industryType->save();
				// save display columns settings for mix entity
				$displayColumnsSettings = new DisplayColumnsSettings($this->db, $industryTypeId);
				$value = $this->getFromPost('browseCategoryMix_id');
				$columnsDisplayValue = implode(",", $value);
				// insert
				$displayColumnsSettings->setBrowseCategoryEntityId($browseCategoryMix->id);
				$displayColumnsSettings->setValue($columnsDisplayValue); 
				$displayColumnsSettings->save(); 
                // redirect
                header("Location: ?action=browseCategory&category=tables&bookmark=industryType&notify=55");
            } else {						
                $notifyc = new Notify(null, $this->db);
                $notify = $notifyc->getPopUpNotifyMessage(401);
                $this->smarty->assign("notify", $notify);						
                $this->smarty->assign('violationList', $violationList);
                $this->smarty->assign('data', $post);
            }
        }
		$this->smarty->assign("currentOperation","addItem");
		$this->smarty->assign('tpl', 'tpls/addIndustryTypeClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount = $this->getFromRequest('itemsCount');
		$itemForDelete = array();

		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
                $industrytype = new IndustryType($this->db, $this->getFromRequest('item_'.$i));
				$item["id"]	= $industrytype->id;
				$item["name"] = $industrytype->type;
				$itemForDelete[] = $item;
			}
		}
		$this->smarty->assign("gobackAction","browseCategory");
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount = $this->getFromRequest('itemsCount');

		for ($i=0; $i<$itemsCount; $i++) {
			$industrytype = new IndustryType($this->db, $this->getFromRequest('item_'.$i));
            $industrytype->delete();
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
	
	protected function actionLoadDisplayColumnsSettings() {

		$entity = $this->getFromRequest('entity');		
        // get browse category list
		switch ($entity) {
			case "mix" :
				$browseCategoryEntity = new BrowseCategoryEntity($this->db); 
				$browseCategoryMix = $browseCategoryEntity->getBrowseCategoryMix();
				$mixColumnsDisplayDefault = explode(',', $browseCategoryMix->default_value);
                // get label text
                $companyLevelLabel = new CompanyLevelLabel($this->db);
                $industryType = new IndustryType($this->db, $this->getFromRequest('industryTypeId'));
                // Product Name
                $companyLevelLabelProductNameDefault = $companyLevelLabel->getProductNameLabel();
                $productNameLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelProductNameDefault->label_id)->getLabelText(); 
                // Add Job
                $companyLevelLabelAddJobDefault = $companyLevelLabel->getAddJobLabel();
                $addJobLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelAddJobDefault->label_id)->getLabelText(); 
                // Description
                $companyLevelLabelDescriptionDefault = $companyLevelLabel->getDescriptionLabel();
                $descriptionLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelDescriptionDefault->label_id)->getLabelText(); 
                // R/O Description
                $companyLevelLabelRODescriptionDefault = $companyLevelLabel->getRODescriptionLabel();
                $roDescriptionLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelRODescriptionDefault->label_id)->getLabelText();
                // R/O VIN Number
                $companyLevelLabelROVinNumberDefault = $companyLevelLabel->getROVinNumberLabel();
                $roVinNumberLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelROVinNumberDefault->label_id)->getLabelText();
                // Contact
                $companyLevelLabelContactDefault = $companyLevelLabel->getContactLabel();
                $contactLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelContactDefault->label_id)->getLabelText();
                // Voc
                $companyLevelLabelVocDefault = $companyLevelLabel->getVocLabel();
                $vocLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelVocDefault->label_id)->getLabelText();
                // Creation Date
                $companyLevelLabelCreationDateDefault = $companyLevelLabel->getCreationDateLabel();
                $creationDateLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelCreationDateDefault->label_id)->getLabelText();
                // Unit Type
                $companyLevelLabelUnitTypeDefault = $companyLevelLabel->getUnitTypeLabel();
                $unitTypeLabel = $industryType->getLabelManager()->getLabel($companyLevelLabelUnitTypeDefault->label_id)->getLabelText();
                
                $columnsSettingsMixValue = $industryType->getDisplayColumnsManager()->getDisplayColumnsSettings($browseCategoryMix->name)->getValue();
                $mixColumnsDisplay = explode(',', $columnsSettingsMixValue);
                $mixColumn4DisplayDefault = array();
                foreach ($mixColumnsDisplayDefault as $columnId) {
                    $mixColumn4DisplayDefault[$columnId] = $industryType->getLabelManager()->getLabel($columnId)->getLabelText();
                }

				$this->smarty->assign('columnsDefaultDisplay', $mixColumn4DisplayDefault);
				$this->smarty->assign('columnsDisplay', $mixColumnsDisplay);
			break;	
		}
		echo $this->smarty->fetch('tpls/manageColumnsDisplaySettings.tpl');
    }
    
	protected function actionSaveDisplayColumnsSettings() {

		$entity = $this->getFromRequest('entity');		
		$rowsToSave = $this->getFromRequest('rowsToSave'); 
        $companyLevelLabel = new CompanyLevelLabel($this->db);
        $industryType = new IndustryType($this->db, $this->getFromRequest('industryTypeId'));
        // get browse category list
		switch ($entity) {
			case "mix" :
                $mixColumn4Display = array();
                foreach ($rowsToSave as $columnId) {
                    $mixColumn4Display[] = $industryType->getLabelManager()->getLabel($columnId)->getLabelText();
                }
				$response = implode(",", $mixColumn4Display);
				foreach ($rowsToSave as $value) {
					$response .= "<input type='hidden' name='browseCategoryMix_id[]' id='browseCategoryMix_id[]' value='$value' />";
				}
			break;	
		} 
		echo $response;
    }
}
?>