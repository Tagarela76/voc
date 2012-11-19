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
        $companyLabelManager = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
        // for repair order
        $companyLevelLabelRepairOrderDefault = $companyLevelLabel->getRepairOrderLabel(); 
        $companyLevelLabelRepairOrder = $companyLabelManager->getLabel($companyLevelLabelRepairOrderDefault->label_id);
        if ($companyLevelLabelRepairOrder) {
			 $repairOrderLabel = $companyLevelLabelRepairOrder->getLabelText();
		} else {
			$repairOrderLabel = $companyLevelLabelRepairOrderDefault->default_label_text;
		} 
        // Mix Browse Category Labels
        // Product Name
        $companyLevelLabelProductNameDefault = $companyLevelLabel->getProductNameLabel();
        $companyLevelLabelProductName = $companyLabelManager->getLabel($companyLevelLabelProductNameDefault->label_id);
        if ($companyLevelLabelProductName) {
			 $productNameLabel = $companyLevelLabelProductName->getLabelText();
		} else {
			$productNameLabel = $companyLevelLabelProductNameDefault->default_label_text;
		}
        // Add Job
        $companyLevelLabelAddJobDefault = $companyLevelLabel->getAddJobLabel();
        $companyLevelLabelAddJob = $companyLabelManager->getLabel($companyLevelLabelAddJobDefault->label_id);
        if ($companyLevelLabelAddJob) {
			 $addJobLabel = $companyLevelLabelAddJob->getLabelText();
		} else {
			$addJobLabel = $companyLevelLabelAddJobDefault->default_label_text;
		}
        // Description
        $companyLevelLabelDescriptionDefault = $companyLevelLabel->getDescriptionLabel();
        $companyLevelLabelDescription = $companyLabelManager->getLabel($companyLevelLabelDescriptionDefault->label_id);
        if ($companyLevelLabelDescription) {
			 $descriptionLabel = $companyLevelLabelDescription->getLabelText();
		} else {
			$descriptionLabel = $companyLevelLabelDescriptionDefault->default_label_text;
		}
        // R/O Description
        $companyLevelLabelRODescriptionDefault = $companyLevelLabel->getRODescriptionLabel();
        $companyLevelLabelRODescription = $companyLabelManager->getLabel($companyLevelLabelRODescriptionDefault->label_id);
        if ($companyLevelLabelRODescription) {
			 $roDescriptionLabel = $companyLevelLabelRODescription->getLabelText();
		} else {
			$roDescriptionLabel = $companyLevelLabelRODescriptionDefault->default_label_text;
		}
        // R/O VIN Number
        $companyLevelLabelROVinNumberDefault = $companyLevelLabel->getROVinNumberLabel();
        $companyLevelLabelROVinNumber = $companyLabelManager->getLabel($companyLevelLabelROVinNumberDefault->label_id);
        if ($companyLevelLabelROVinNumber) {
			$roVinNumberLabel = $companyLevelLabelROVinNumber->getLabelText();
		} else {
			$roVinNumberLabel = $companyLevelLabelROVinNumberDefault->default_label_text;
		}
        // Contact
        $companyLevelLabelContactDefault = $companyLevelLabel->getContactLabel();
        $companyLevelLabelContact = $companyLabelManager->getLabel($companyLevelLabelContactDefault->label_id);
        if ($companyLevelLabelContact) {
			$contactLabel = $companyLevelLabelContact->getLabelText();
		} else {
			$contactLabel = $companyLevelLabelContactDefault->default_label_text;
		}
        // Voc
        $companyLevelLabelVocDefault = $companyLevelLabel->getVocLabel();
        $companyLevelLabelVoc = $companyLabelManager->getLabel($companyLevelLabelVocDefault->label_id);
        if ($companyLevelLabelVoc) {
			$vocLabel = $companyLevelLabelVoc->getLabelText();
		} else {
			$vocLabel = $companyLevelLabelVocDefault->default_label_text;
		}
        // Creation Date
        $companyLevelLabelCreationDateDefault = $companyLevelLabel->getCreationDateLabel();
        $companyLevelLabelCreationDate = $companyLabelManager->getLabel($companyLevelLabelCreationDateDefault->label_id);
        if ($companyLevelLabelCreationDate) {
			$creationDateLabel = $companyLevelLabelCreationDate->getLabelText();
		} else {
			$creationDateLabel = $companyLevelLabelCreationDateDefault->default_label_text;
		}
        // Unit Type
        $companyLevelLabelUnitTypeDefault = $companyLevelLabel->getUnitTypeLabel();
        $companyLevelLabelUnitType = $companyLabelManager->getLabel($companyLevelLabelUnitTypeDefault->label_id);
        if ($companyLevelLabelUnitType) {
			$unitTypeLabel = $companyLevelLabelUnitType->getLabelText();
		} else {
			$unitTypeLabel = $companyLevelLabelUnitTypeDefault->default_label_text;
		}
        
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
        
		// get browse category list
		$browseCategoryEntity = new BrowseCategoryEntity($this->db);
		$browseCategoryMix = $browseCategoryEntity->getBrowseCategoryMix(); 
        $browseCategoryMixDefaultValue = $browseCategoryMix->default_value;
        $browseCategoryMixDefaultValueArray = explode(",", $browseCategoryMixDefaultValue);
		$displayColumnsSettings = new DisplayColumnsSettings($this->db, $this->getFromRequest('id'));
		$columnsSettingsMixValue = '';
		$columnsSettingsMix = $displayColumnsSettings->getDisplayColumnsSettings($browseCategoryMix->name); 

		if ($columnsSettingsMix) {
			$columnsSettingsMixValue = $columnsSettingsMix->getValue();
		} else {
			$columnsSettingsMixValue = $browseCategoryMixDefaultValue;
		} 
        $columnsSettingsMixValueArray = explode(",", $columnsSettingsMixValue);
        
        $labels = array($productNameLabel, $addJobLabel, $descriptionLabel, 
            $roDescriptionLabel, $contactLabel, $roVinNumberLabel, 
            $vocLabel, $unitTypeLabel, $creationDateLabel);
        $mixColumn4Display = array();
        foreach ($browseCategoryMixDefaultValueArray as $key=> $value) {
            if (in_array($value, $columnsSettingsMixValueArray)) {
                $mixColumn4Display[] = $labels[$key];
            }
        }
        $columnsSettingsMixValue = implode(",", $mixColumn4Display);
       // var_dump($mixColumn4Display); die();
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
        $companyLevelLabelRepairOrder = $companyLabelManager->getLabel($companyLevelLabelRepairOrderDefault->label_id); 
        if ($companyLevelLabelRepairOrder) {
			 $repairOrderLabel = $companyLevelLabelRepairOrder->getLabelText();
		} else {
			$repairOrderLabel = $companyLevelLabelRepairOrderDefault->default_label_text;
		} 
        // Mix Browse Category Labels
        // Product Name
        $companyLevelLabelProductNameDefault = $companyLevelLabel->getProductNameLabel();
        $companyLevelLabelProductName = $companyLabelManager->getLabel($companyLevelLabelProductNameDefault->label_id);
        if ($companyLevelLabelProductName) {
			 $productNameLabel = $companyLevelLabelProductName->getLabelText();
		} else {
			$productNameLabel = $companyLevelLabelProductNameDefault->default_label_text;
		}
        // Add Job
        $companyLevelLabelAddJobDefault = $companyLevelLabel->getAddJobLabel();
        $companyLevelLabelAddJob = $companyLabelManager->getLabel($companyLevelLabelAddJobDefault->label_id);
        if ($companyLevelLabelAddJob) {
			 $addJobLabel = $companyLevelLabelAddJob->getLabelText();
		} else {
			$addJobLabel = $companyLevelLabelAddJobDefault->default_label_text;
		}
        // Description
        $companyLevelLabelDescriptionDefault = $companyLevelLabel->getDescriptionLabel();
        $companyLevelLabelDescription = $companyLabelManager->getLabel($companyLevelLabelDescriptionDefault->label_id);
        if ($companyLevelLabelDescription) {
			 $descriptionLabel = $companyLevelLabelDescription->getLabelText();
		} else {
			$descriptionLabel = $companyLevelLabelDescriptionDefault->default_label_text;
		}
        // R/O Description
        $companyLevelLabelRODescriptionDefault = $companyLevelLabel->getRODescriptionLabel();
        $companyLevelLabelRODescription = $companyLabelManager->getLabel($companyLevelLabelRODescriptionDefault->label_id);
        if ($companyLevelLabelRODescription) {
			 $roDescriptionLabel = $companyLevelLabelRODescription->getLabelText();
		} else {
			$roDescriptionLabel = $companyLevelLabelRODescriptionDefault->default_label_text;
		}
        // R/O VIN Number
        $companyLevelLabelROVinNumberDefault = $companyLevelLabel->getROVinNumberLabel();
        $companyLevelLabelROVinNumber = $companyLabelManager->getLabel($companyLevelLabelROVinNumberDefault->label_id);
        if ($companyLevelLabelROVinNumber) {
			$roVinNumberLabel = $companyLevelLabelROVinNumber->getLabelText();
		} else {
			$roVinNumberLabel = $companyLevelLabelROVinNumberDefault->default_label_text;
		}
        // Contact
        $companyLevelLabelContactDefault = $companyLevelLabel->getContactLabel();
        $companyLevelLabelContact = $companyLabelManager->getLabel($companyLevelLabelContactDefault->label_id);
        if ($companyLevelLabelContact) {
			$contactLabel = $companyLevelLabelContact->getLabelText();
		} else {
			$contactLabel = $companyLevelLabelContactDefault->default_label_text;
		}
        // Voc
        $companyLevelLabelVocDefault = $companyLevelLabel->getVocLabel();
        $companyLevelLabelVoc = $companyLabelManager->getLabel($companyLevelLabelVocDefault->label_id);
        if ($companyLevelLabelVoc) {
			$vocLabel = $companyLevelLabelVoc->getLabelText();
		} else {
			$vocLabel = $companyLevelLabelVocDefault->default_label_text;
		}
        // Creation Date
        $companyLevelLabelCreationDateDefault = $companyLevelLabel->getCreationDateLabel();
        $companyLevelLabelCreationDate = $companyLabelManager->getLabel($companyLevelLabelCreationDateDefault->label_id);
        if ($companyLevelLabelCreationDate) {
			$creationDateLabel = $companyLevelLabelCreationDate->getLabelText();
		} else {
			$creationDateLabel = $companyLevelLabelCreationDateDefault->default_label_text;
		}
        // Unit Type
        $companyLevelLabelUnitTypeDefault = $companyLevelLabel->getUnitTypeLabel();
        $companyLevelLabelUnitType = $companyLabelManager->getLabel($companyLevelLabelUnitTypeDefault->label_id);
        if ($companyLevelLabelUnitType) {
			$unitTypeLabel = $companyLevelLabelUnitType->getLabelText();
		} else {
			$unitTypeLabel = $companyLevelLabelUnitTypeDefault->default_label_text;
		}
        
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
        
		// get browse category list
		$browseCategoryEntity = new BrowseCategoryEntity($this->db);
		$browseCategoryMix = $browseCategoryEntity->getBrowseCategoryMix(); 
		$displayColumnsSettings = new DisplayColumnsSettings($this->db, $this->getFromRequest('id'));
		$columnsSettingsMixValue = '';
		$columnsSettingsMix = $displayColumnsSettings->getDisplayColumnsSettings($browseCategoryMix->name); 
        $browseCategoryMixDefaultValueArray = explode(",", $browseCategoryMix->default_value);
		if ($columnsSettingsMix) {
			 $columnsSettingsMixValue = $columnsSettingsMix->getValue();
		} else {
			$columnsSettingsMixValue = $browseCategoryMix->default_value;
		} 
		$columnsSettingsMixValueArray = explode(',', $columnsSettingsMixValue);

        $labels = array($productNameLabel, $addJobLabel, $descriptionLabel, 
            $roDescriptionLabel, $contactLabel, $roVinNumberLabel, 
            $vocLabel, $unitTypeLabel, $creationDateLabel);
        $mixColumn4Display = array();
        foreach ($browseCategoryMixDefaultValueArray as $key=> $value) {
            if (in_array($value, $columnsSettingsMixValueArray)) {
                $mixColumn4Display[] = $labels[$key];
            }
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
            $defaultLabels = array();
            foreach ($value as $labelText) {
                $defaultLabels[] = $companyLabelManager->getDefaultLabelByLabelText($labelText);
            }
			$columnsDisplayValue = implode(",", $defaultLabels);

			// we should knew - insert/update. So i get columns settings and set display columns settings id
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
                    if (!$companyLevelLabelRepairOrder) {
                        $companyLevelLabelRepairOrder = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
                        $companyLevelLabelRepairOrder->setCompanyLevelLabelId($companyLevelLabelRepairOrderDefault->id);
                    } 
                    
                    $companyLevelLabelRepairOrder->setLabelText($post[$companyLevelLabelRepairOrderDefault->label_id]);
                    $companyLevelLabelRepairOrder->save();
                    
                    if (!$companyLevelLabelProductName) {
                        $companyLevelLabelProductName = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
                        $companyLevelLabelProductName->setCompanyLevelLabelId($companyLevelLabelProductNameDefault->id);
                    }                   
                    $companyLevelLabelProductName->setLabelText($post[$companyLevelLabelProductNameDefault->label_id]);
                    $companyLevelLabelProductName->save();
                    
                    if (!$companyLevelLabelAddJob) {
                        $companyLevelLabelAddJob = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
                        $companyLevelLabelAddJob->setCompanyLevelLabelId($companyLevelLabelAddJobDefault->id);
                    }  
                    $companyLevelLabelAddJob->setLabelText($post[$companyLevelLabelAddJobDefault->label_id]);
                    $companyLevelLabelAddJob->save();
                    
                    if (!$companyLevelLabelDescription) {
                        $companyLevelLabelDescription = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
                        $companyLevelLabelDescription->setCompanyLevelLabelId($companyLevelLabelDescriptionDefault->id);
                    }
                    $companyLevelLabelDescription->setLabelText($post[$companyLevelLabelDescriptionDefault->label_id]);
                    $companyLevelLabelDescription->save();
                    
                    if (!$companyLevelLabelRODescription) {
                        $companyLevelLabelRODescription = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
                        $companyLevelLabelRODescription->setCompanyLevelLabelId($companyLevelLabelRODescriptionDefault->id); 
                    }
                    $companyLevelLabelRODescription->setLabelText($post[$companyLevelLabelRODescriptionDefault->label_id]);
                    $companyLevelLabelRODescription->save();
                    
                    if (!$companyLevelLabelROVinNumber) {
                        $companyLevelLabelROVinNumber = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
                        $companyLevelLabelROVinNumber->setCompanyLevelLabelId($companyLevelLabelROVinNumberDefault->id);
                    }
                    $companyLevelLabelROVinNumber->setLabelText($post[$companyLevelLabelROVinNumberDefault->label_id]);
                    $companyLevelLabelROVinNumber->save();
                    
                    if (!$companyLevelLabelContact) {
                        $companyLevelLabelContact = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
                        $companyLevelLabelContact->setCompanyLevelLabelId($companyLevelLabelContactDefault->id); 
                    }
                    $companyLevelLabelContact->setLabelText($post[$companyLevelLabelContactDefault->label_id]);
                    $companyLevelLabelContact->save();
                    
                    if (!$companyLevelLabelVoc) {
                        $companyLevelLabelVoc= new CompanyLabelManager($this->db, $this->getFromRequest('id'));
                        $companyLevelLabelVoc->setCompanyLevelLabelId($companyLevelLabelVocDefault->id);
                    }
                    $companyLevelLabelVoc->setLabelText($post[$companyLevelLabelVocDefault->label_id]);
                    $companyLevelLabelVoc->save();
                    
                    if (!$companyLevelLabelCreationDate) {
                        $companyLevelLabelCreationDate = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
                        $companyLevelLabelCreationDate->setCompanyLevelLabelId($companyLevelLabelCreationDateDefault->id);
                    }
                    $companyLevelLabelCreationDate->setLabelText($post[$companyLevelLabelCreationDateDefault->label_id]);
                    $companyLevelLabelCreationDate->save();
                    
                    if (!$companyLevelLabelUnitType) {
                        $companyLevelLabelUnitType = new CompanyLabelManager($this->db, $this->getFromRequest('id'));
                        $companyLevelLabelUnitType->setCompanyLevelLabelId($companyLevelLabelUnitTypeDefault->id);
                    }
                    $companyLevelLabelUnitType->setLabelText($post[$companyLevelLabelUnitTypeDefault->label_id]);
                    $companyLevelLabelUnitType->save();
                    
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
                $companyLabelManager = new CompanyLabelManager($this->db, $this->getFromRequest('industryTypeId'));
                $companyLevelLabel = new CompanyLevelLabel($this->db);
                // Product Name
                $companyLevelLabelProductNameDefault = $companyLevelLabel->getProductNameLabel();
                $companyLevelLabelProductName = $companyLabelManager->getLabel($companyLevelLabelProductNameDefault->label_id);
                if ($companyLevelLabelProductName) {
                     $productNameLabel = $companyLevelLabelProductName->getLabelText();
                } else {
                    $productNameLabel = $companyLevelLabelProductNameDefault->default_label_text;
                }
                // Add Job
                $companyLevelLabelAddJobDefault = $companyLevelLabel->getAddJobLabel();
                $companyLevelLabelAddJob = $companyLabelManager->getLabel($companyLevelLabelAddJobDefault->label_id);
                if ($companyLevelLabelAddJob) {
                     $addJobLabel = $companyLevelLabelAddJob->getLabelText();
                } else {
                    $addJobLabel = $companyLevelLabelAddJobDefault->default_label_text;
                }
                // Description
                $companyLevelLabelDescriptionDefault = $companyLevelLabel->getDescriptionLabel();
                $companyLevelLabelDescription = $companyLabelManager->getLabel($companyLevelLabelDescriptionDefault->label_id);
                if ($companyLevelLabelDescription) {
                     $descriptionLabel = $companyLevelLabelDescription->getLabelText();
                } else {
                    $descriptionLabel = $companyLevelLabelDescriptionDefault->default_label_text;
                }
                // R/O Description
                $companyLevelLabelRODescriptionDefault = $companyLevelLabel->getRODescriptionLabel();
                $companyLevelLabelRODescription = $companyLabelManager->getLabel($companyLevelLabelRODescriptionDefault->label_id);
                if ($companyLevelLabelRODescription) {
                     $roDescriptionLabel = $companyLevelLabelRODescription->getLabelText();
                } else {
                    $roDescriptionLabel = $companyLevelLabelRODescriptionDefault->default_label_text;
                }
                // R/O VIN Number
                $companyLevelLabelROVinNumberDefault = $companyLevelLabel->getROVinNumberLabel();
                $companyLevelLabelROVinNumber = $companyLabelManager->getLabel($companyLevelLabelROVinNumberDefault->label_id);
                if ($companyLevelLabelROVinNumber) {
                    $roVinNumberLabel = $companyLevelLabelROVinNumber->getLabelText();
                } else {
                    $roVinNumberLabel = $companyLevelLabelROVinNumberDefault->default_label_text;
                }
                // Contact
                $companyLevelLabelContactDefault = $companyLevelLabel->getContactLabel();
                $companyLevelLabelContact = $companyLabelManager->getLabel($companyLevelLabelContactDefault->label_id);
                if ($companyLevelLabelContact) {
                    $contactLabel = $companyLevelLabelContact->getLabelText();
                } else {
                    $contactLabel = $companyLevelLabelContactDefault->default_label_text;
                }
                // Voc
                $companyLevelLabelVocDefault = $companyLevelLabel->getVocLabel();
                $companyLevelLabelVoc = $companyLabelManager->getLabel($companyLevelLabelVocDefault->label_id);
                if ($companyLevelLabelVoc) {
                    $vocLabel = $companyLevelLabelVoc->getLabelText();
                } else {
                    $vocLabel = $companyLevelLabelVocDefault->default_label_text;
                }
                // Creation Date
                $companyLevelLabelCreationDateDefault = $companyLevelLabel->getCreationDateLabel();
                $companyLevelLabelCreationDate = $companyLabelManager->getLabel($companyLevelLabelCreationDateDefault->label_id);
                if ($companyLevelLabelCreationDate) {
                    $creationDateLabel = $companyLevelLabelCreationDate->getLabelText();
                } else {
                    $creationDateLabel = $companyLevelLabelCreationDateDefault->default_label_text;
                }
                // Unit Type
                $companyLevelLabelUnitTypeDefault = $companyLevelLabel->getUnitTypeLabel();
                $companyLevelLabelUnitType = $companyLabelManager->getLabel($companyLevelLabelUnitTypeDefault->label_id);
                if ($companyLevelLabelUnitType) {
                    $unitTypeLabel = $companyLevelLabelUnitType->getLabelText();
                } else {
                    $unitTypeLabel = $companyLevelLabelUnitTypeDefault->default_label_text;
                }
				if ($this->getFromRequest('industryTypeId') != 'false') {
					$displayColumnsSettings = new DisplayColumnsSettings($this->db, $this->getFromRequest('industryTypeId'));					
					$columnsSettingsMix = $displayColumnsSettings->getDisplayColumnsSettings($browseCategoryMix->name); 

                    if ($columnsSettingsMix) {
						 $columnsSettingsMixValue = $columnsSettingsMix->getValue();
				    } else {
					    $columnsSettingsMixValue = $browseCategoryMix->default_value;
				    }
					$mixColumnsDisplay = explode(',', $columnsSettingsMixValue);
                    $labels = array($productNameLabel, $addJobLabel, $descriptionLabel, 
                            $roDescriptionLabel, $contactLabel, $roVinNumberLabel, 
                            $vocLabel, $unitTypeLabel, $creationDateLabel);
                    $mixColumn4Display = array();
                    $mixColumn4DisplayDefault = array();
                    foreach ($mixColumnsDisplayDefault as $key=> $value) {
                        if (in_array($value, $mixColumnsDisplay)) {
                            $mixColumn4Display[] = $labels[$key];
                        }
                        $mixColumn4DisplayDefault[] = $labels[$key];
                    }
                    $mixColumnsDisplay = $mixColumn4Display;
                    $mixColumnsDisplayDefault = $mixColumn4DisplayDefault;
				} else {
					$mixColumnsDisplay = $mixColumnsDisplayDefault;
				}

				$this->smarty->assign('columnsDefaultDisplay', $mixColumnsDisplayDefault);
				$this->smarty->assign('columnsDisplay', $mixColumnsDisplay);
			break;	
		}
		echo $this->smarty->fetch('tpls/manageColumnsDisplaySettings.tpl');
    }
    
	protected function actionSaveDisplayColumnsSettings() {

		$entity = $this->getFromRequest('entity');		
		$rowsToSave = $this->getFromRequest('rowsToSave'); 
        // get browse category list
		switch ($entity) {
			case "mix" :
				$response = implode(",", $rowsToSave);
				foreach ($rowsToSave as $value) {
					$response .= "<input type='hidden' name='browseCategoryMix_id[]' id='browseCategoryMix_id[]' value='$value' />";
				}
			break;	
		} 
		echo $response;
    }
}
?>