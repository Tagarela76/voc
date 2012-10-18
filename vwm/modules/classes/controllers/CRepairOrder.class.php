<?php
use VWM\Label\CompanyLevelLabel;

class CRepairOrder extends Controller {

    function CRepairOrder($smarty, $xnyo, $db, $user, $action) {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'repairOrder';
        $this->parent_category = 'facility';
    }

    protected function actionViewDetails() {

        $repairOrder = new RepairOrder($this->db, $this->getFromRequest('id'));
        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $params = array("bookmark" => "repairOrder");

		$mixList = array();
        // get child mixes 
        $mixTotalPrice = 0;
        $mixes = $repairOrder->getMixes(); 
		foreach ($mixes as $mix) {
			$mixOptimized = new MixOptimized($this->db, $mix->mix_id);
			$mix->price = $mixOptimized->getMixPrice();
			$mixTotalPrice += $mix->price;
			$mixTotalSpentTime += $mix->spent_time;
			$mixList[] = $mix;
		}
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest("facilityID"));
		$companyId = $facilityDetails["company_id"];
		$labelCompanySystem = new CompanyLevelLabel($this->db, $companyId);
		$repairOrderLabel = $labelCompanySystem->getRepairOrderLabel();
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);
		
        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setPermissionsNew('viewRepairOrder');
		
        $this->smarty->assign('backUrl', '?action=browseCategory&category=facility&id=' . $this->getFromRequest('facilityID') . '&bookmark=repairOrder');
        $this->smarty->assign('deleteUrl', '?action=deleteItem&category=repairOrder&id=' . $this->getFromRequest('id') . '&facilityID=' . $this->getFromRequest("facilityID"));
        $this->smarty->assign('editUrl', '?action=edit&category=repairOrder&id=' . $this->getFromRequest('id') . '&facilityID=' . $this->getFromRequest("facilityID"));
        $this->smarty->assign('mixList', $mixList);
		$this->smarty->assign('repairOrder', $repairOrder);
		$this->smarty->assign('mixTotalPrice', $mixTotalPrice);
		$this->smarty->assign('mixTotalSpentTime', $mixTotalSpentTime);
                
        //set tpl
        $this->smarty->assign('tpl', 'tpls/viewRepairOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * bookmarkRepairOrder($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
    protected function bookmarkRepairOrder($vars) {

        extract($vars);
        if (is_null($facilityDetails['facility_id'])) {
            throw new Exception('404');
        }
            
        $facility = new Facility($this->db);
        //	set search criteria
		if (!is_null($this->getFromRequest('q'))) {
			$facility->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		}

        $repairOrderCount = $facility->countRepairOrderInFacility($facilityDetails['facility_id']);
        $url = "?".$_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/","", $url);
        $pagination = new Pagination($repairOrderCount);
		$pagination->url = $url; 
        $this->smarty->assign('pagination', $pagination);
        
        $repairOrderList = $facility->getRepairOrdersList($facilityDetails['facility_id'],$pagination);
        if ($repairOrderList) {
            for ($i = 0; $i < count($repairOrderList); $i++) {
                $url = "?action=viewDetails&category=repairOrder&id=" . $repairOrderList[$i]->id . "&facilityID=" . $facilityDetails['facility_id'];				
                $repairOrderList[$i]->url = $url;
            }
        }
		
        $this->smarty->assign("childCategoryItems", $repairOrderList);

        //	set js scripts
        $jsSources = array(
            'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
            'modules/js/checkBoxes.js',
			'modules/js/autocomplete/jquery.autocomplete.js');
        $this->smarty->assign('jsSources', $jsSources);

        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
        $this->smarty->assign('cssSources', $cssSources);

        //	set tpl
        $this->smarty->assign('tpl', 'tpls/repairOrderList.tpl');
    }

    protected function actionAddItem() {
        //	Access control
        if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
            throw new Exception('deny');
        }

        $request = $this->getFromRequest();
        $request["id"] = $request["facilityID"];
        $request['parent_id'] = $request['facilityID'];
        $request['parent_category'] = 'facility';
        $this->smarty->assign('request', $request);

        $params = array("bookmark" => "repairOrder");

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setNavigationUpNew('facility', $this->getFromRequest("facilityID"));
        $this->setPermissionsNew('viewFacility');

		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest("facilityID"));
		$companyId = $facilityDetails["company_id"];
		$labelCompanySystem = new CompanyLevelLabel($this->db, $companyId);
		$repairOrderLabel = $labelCompanySystem->getRepairOrderLabel();
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);

        //	set js scripts
        $jsSources = array(
            'modules/js/saveItem.js',
            'modules/js/PopupWindow.js'
        );
        $this->smarty->assign('jsSources', $jsSources);

        $this->smarty->assign('pleaseWaitReason', "Recalculating repair orders at facility.");
        $this->smarty->assign('tpl', 'tpls/addRepairOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionDeleteItem() {

        $req_id = $this->getFromRequest('id');
        if (!is_array($req_id))
            $req_id = array($req_id);
        $itemForDelete = array();
        if (!is_null($this->getFromRequest('id'))) {
            foreach ($req_id as $repairOrderID) {
                //	Access control
                if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
                    throw new Exception('deny');
                }
                $repairOrder = new RepairOrder($this->db, $repairOrderID);
                $delete = array();
                $delete["id"] = $repairOrder->id;
                $delete["number"] = $repairOrder->number;
                $delete["description"] = $repairOrder->description;
                $delete["customer_name"] = $repairOrder->customer_name;
                $delete["status"] = $repairOrder->status;
                $delete["facility_id"] = $repairOrder->facility_id;
				$delete["vin"] = $repairOrder->vin;
                $itemForDelete[] = $delete;
            }
        }
        if (!is_null($this->getFromRequest('facilityID'))) {
            $this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=" . $this->getFromRequest('facilityID') . "&bookmark=repairOrder");
            //as ShowAddItem
            $params = array("bookmark" => "repairOrder");

            $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
            $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
            $this->setPermissionsNew('viewFacility');
        }

        $this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
    }

    protected function actionConfirmDelete() {

        foreach ($this->itemID as $ID) {

            $repairOrder = new RepairOrder($this->db, $ID);
            $facilityId = $repairOrder->facility_id;
            // get work order mix id, we check if work order already has any mixes
            $mixOptimized = new MixOptimized($this->db);
            $mixIDs = $repairOrder->getMixes();
            if (count($mixIDs) < 2) {
                // we can delete only empty work order
                $repairOrder->delete();
                // delete empty mix
                $mixOptimized = new MixOptimized($this->db, $woId);
                $mixOptimized->delete();
            } else {
                header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=repairOrder&notify=49");
                die();
            }
        }

        if ($this->successDeleteInventories)
            header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=repairOrder&notify=48");
    }

    protected function actionEdit() {

        //	Access control
        if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
            throw new Exception('deny');
        }
        $repairOrder = new RepairOrder($this->db, $this->getFromRequest('id'));
        $this->smarty->assign('data', $repairOrder);

        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $params = array("bookmark" => "repairOrder");

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setPermissionsNew('viewRepairOrder');

		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest("facilityID"));
		$companyId = $facilityDetails["company_id"];
		$labelCompanySystem = new CompanyLevelLabel($this->db, $companyId);
		$repairOrderLabel = $labelCompanySystem->getRepairOrderLabel();
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);
		
        //	set js scripts
        $jsSources = array(
            'modules/js/reg_country_state.js',
            'modules/js/saveItem.js',
            'modules/js/PopupWindow.js',
            'modules/js/addJobberPopups.js',
            'modules/js/checkBoxes.js'
        );
        $this->smarty->assign('jsSources', $jsSources);

        $this->smarty->assign('tpl', 'tpls/addRepairOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

	protected function actionCreateLabel() {
		
		$repairOrder = new RepairOrder($this->db, $this->getFromRequest('id'));
		$mixList = array();
        // get child mixes 
		$mixTotalPrice = 0;
        $mixes = $repairOrder->getMixes();
		foreach ($mixes as $mix) {
			$mixOptimized = new MixOptimized($this->db, $mix->mix_id);
			$mix->price = $mixOptimized->getMixPrice();
			$mixTotalPrice += $mix->price;
			$mixList[] = $mix;
		}

		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($repairOrder->facility_id);
		$companyId = $facilityDetails["company_id"];
		$labelCompanySystem = new CompanyLevelLabel($this->db, $companyId);
		$repairOrderLabel = $labelCompanySystem->getRepairOrderLabel();
		$this->smarty->assign('repairOrderLabel', $repairOrderLabel);
		
		$this->smarty->assign('mixTotalPrice', $mixTotalPrice);
        $this->smarty->assign('repairOrder', $repairOrder);
        $this->smarty->assign('mixList', $mixList);

		$this->smarty->display("tpls/repairOrderLabel.tpl");
	}

}

?>