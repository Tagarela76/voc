<?php

class CPfpTypes extends Controller {

    function CPfpTypes($smarty, $xnyo, $db, $user, $action) {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'PfpTypes';
        $this->parent_category = 'company';
    }

    function runAction() {

        $this->runCommon();
        $functionName = 'action' . ucfirst($this->action);
        if (method_exists($this, $functionName))
            $this->$functionName();
    }
    
	/**
     * bookmarkWorkOrder($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
    protected function bookmarkPfpTypes($vars) {

        extract($vars);
        if (is_null($facilityDetails['facility_id'])) {
            throw new Exception('404');
        }
        
		$facility = new Facility($this->db);
		$pfpTypesList = $facility->getPfpTypes($facilityDetails['facility_id']);
        if ($pfpTypesList) {
            for ($i = 0; $i < count($pfpTypesList); $i++) {
                $url = "?action=viewDetails&category=pfpTypes&id=" . $pfpTypesList[$i]->id . "&facilityID=" . $facilityDetails['facility_id'];
                $pfpTypesList[$i]->url = $url;
            }
        }
        $this->smarty->assign("childCategoryItems", $pfpTypesList);

        //	set js scripts
        $jsSources = array(
            'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
            'modules/js/checkBoxes.js');
        $this->smarty->assign('jsSources', $jsSources);

        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
        $this->smarty->assign('cssSources', $cssSources);

        //	set tpl
		$this->smarty->assign('tpl', 'tpls/pfpTypesList.tpl');
    }

    private function actionAddItem() {
        //	Access control
        if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
            throw new Exception('deny');
        }

        $request = $this->getFromRequest();
        $request["id"] = $request["facilityID"];
        $request['parent_id'] = $request['facilityID'];
        $request['parent_category'] = 'facility';
        $this->smarty->assign('request', $request);

        $params = array("bookmark" => "workOrder");

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setNavigationUpNew('facility', $this->getFromRequest("facilityID"));
        $this->setPermissionsNew('viewFacility');

        //	set js scripts
        $jsSources = array(
            'modules/js/saveItem.js',
            'modules/js/PopupWindow.js'
        );
        $this->smarty->assign('jsSources', $jsSources);

        $this->smarty->assign('pleaseWaitReason', "Please wait.");
        $this->smarty->assign('tpl', 'tpls/addPfpTypes.tpl');
        $this->smarty->display("tpls:index.tpl");
    }
	
    private function actionDeleteItem() {

        $req_id = $this->getFromRequest('id');
        if (!is_array($req_id))
            $req_id = array($req_id);
        $itemForDelete = array();
        if (!is_null($this->getFromRequest('id'))) {
            foreach ($req_id as $pfptype) {
                //	Access control
                if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
                    throw new Exception('deny');
                }
                $pfptypes = new PfpTypes($this->db, $pfptype);
                $delete = array();
                $delete["id"] = $pfptypes->id;
                $delete["name"] = $pfptypes->name;
                $delete["facility_id"] = $pfptypes->facility_id;
                $itemForDelete[] = $delete;
            }
        }
        if (!is_null($this->getFromRequest('facilityID'))) {
            $this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=" . $this->getFromRequest('facilityID') . "&bookmark=pfpTypes");
            //as ShowAddItem
            $params = array("bookmark" => "pfpTypes");

            $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
            $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
            $this->setPermissionsNew('viewFacility');
        }

        $this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
    }
    
    private function actionViewDetails() {

        $pfpTypes = new PfpTypes($this->db, $this->getFromRequest('id'));    
        $pfpProducts = $pfpTypes->getPfpProductsByTypeId();

        $url = "?".$_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/","", $url);
        $pagination = new Pagination(count($pfpProducts));
		$pagination->url = $url; 
        $this->smarty->assign('pagination', $pagination);
 //  var_dump($pagination); die();     
        $pfps = $pfpTypes->getPfpProductsByTypeId($pagination);
        $pfp = new PFPManager($this->db);
        $pfpList = $pfp->getUnAssignPFP2Type();
       //   var_dump($pfpList); die();  
        $this->smarty->assign('pfpList', $pfpList);
        $this->smarty->assign('pfpTypes', $pfpTypes);
        $this->smarty->assign('pfps', $pfps);
        
        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $params = array("bookmark" => "pfpTypes");
        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);

        $this->smarty->assign('pfps', $pfps);
        $this->setPermissionsNew('viewData');
        $this->smarty->assign('backUrl', '?action=browseCategory&category=facility&id=' . $this->getFromRequest('facilityID') . '&bookmark=pfpTypes');
        $this->smarty->assign('deleteUrl', '?action=deleteItem&category=pfpTypes&id=' . $this->getFromRequest('id') . '&facilityID=' . $this->getFromRequest("facilityID"));

        //set js scripts
        $jsSources = array('modules/js/checkBoxes.js',
            'modules/js/autocomplete/jquery.autocomplete.js',
            'modules/js/pfpTypes.js');
        $this->smarty->assign('jsSources', $jsSources);
        //set tpl
        $this->smarty->assign('tpl', 'tpls/viewPfpType.tpl');
        $this->smarty->display("tpls:index.tpl");
    }
    
	private function actionConfirmDelete() {

        foreach ($this->itemID as $ID) {

            $pfpTypes = new PfpTypes($this->db, $ID);
            $facilityId = $pfpTypes->facility_id;
            $pfpTypes->delete();
        }

        if ($this->successDeleteInventories)
            header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=pfpTypes&notify=50");
    }
    
    private function actionAssign() {

        $pfpID = $this->getFromRequest('pfpID');
        $pfpTypeid = $this->getFromRequest('id');
        $facilityID = $this->getFromRequest('facilityID');
        $pfp = new PFPManager($this->db);
        $pfp->assignPFP2Type($pfpID, $pfpTypeid);
        $url = "?action=viewDetails&category=pfpTypes&id={$pfpTypeid}&facilityID={$facilityID}";
        echo $url;
    }
    
    private function actionUnassign() {

        $pfpIDs = $this->getFromRequest('pfpIDs');
        $pfpTypeid = $this->getFromRequest('id');
        $facilityID = $this->getFromRequest('facilityID');
        $pfp = new PFPManager($this->db);
        foreach ($pfpIDs as $pfpID) {
            $pfp->unAssignPFP2Type($pfpID);
        }
        $url = "?action=viewDetails&category=pfpTypes&id={$pfpTypeid}&facilityID={$facilityID}";
        echo $url;
    }

}

?>