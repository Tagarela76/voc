<?php

class CWorkOrder extends Controller {

    function CWorkOrder($smarty, $xnyo, $db, $user, $action) {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'WorkOrder';
        $this->parent_category = 'facility';
    }

    function runAction() {

        $this->runCommon();
        $functionName = 'action' . ucfirst($this->action);
        if (method_exists($this, $functionName))
            $this->$functionName();
    }

    private function actionViewDetails() {

        $workOrder = new WorkOrder($this->db, $this->getFromRequest('id'));
        $this->smarty->assign('workOrder', $workOrder);
        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $params = array("bookmark" => "workOrder");

        // get child mixes 
        $mixList = $workOrder->getMixes();
        
        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setPermissionsNew('viewWorkOrder');

        $this->smarty->assign('backUrl', '?action=browseCategory&category=facility&id=' . $this->getFromRequest('facilityID') . '&bookmark=workOrder');
        $this->smarty->assign('deleteUrl', '?action=deleteItem&category=workOrder&id=' . $this->getFromRequest('id') . '&facilityID=' . $this->getFromRequest("facilityID"));
        $this->smarty->assign('editUrl', '?action=edit&category=workOrder&id=' . $this->getFromRequest('id') . '&facilityID=' . $this->getFromRequest("facilityID"));
        $this->smarty->assign('mixList', $mixList);
        //set js scripts
        $jsSources = array('modules/js/checkBoxes.js',
            'modules/js/autocomplete/jquery.autocomplete.js');
        $this->smarty->assign('jsSources', $jsSources);
        //set tpl
        $this->smarty->assign('tpl', 'tpls/viewWorkOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    /**
     * bookmarkWorkOrder($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
    protected function bookmarkWorkOrder($vars) {

        extract($vars);
        if (is_null($facilityDetails['facility_id'])) {
            throw new Exception('404');
        }
        $filterStr = $this->filterList('workOrder');
        
        $facility = new Facility($this->db);
        
        $workOrderCount = $facility->countWorkOrderInFacility($facilityDetails['facility_id']);
        $url = "?".$_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/","", $url);
        $pagination = new Pagination($workOrderCount);
		$pagination->url = $url; 
        $this->smarty->assign('pagination', $pagination);
        
        $workOrderList = $facility->getWorkOrdersList($facilityDetails['facility_id'],$pagination);
        if ($workOrderList) {
            for ($i = 0; $i < count($workOrderList); $i++) {
                $url = "?action=viewDetails&category=workOrder&id=" . $workOrderList[$i]->id . "&facilityID=" . $facilityDetails['facility_id'];
                $workOrderList[$i]->url = $url;
            }
        }
        $this->smarty->assign("childCategoryItems", $workOrderList);

        //	set js scripts
        $jsSources = array(
            'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
            'modules/js/checkBoxes.js');
        $this->smarty->assign('jsSources', $jsSources);

        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
        $this->smarty->assign('cssSources', $cssSources);

        //	set tpl
        $this->smarty->assign('tpl', 'tpls/workOrderList.tpl');
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

        $this->smarty->assign('pleaseWaitReason', "Recalculating mixes at department.");
        $this->smarty->assign('tpl', 'tpls/addWorkOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    private function actionDeleteItem() {

        $req_id = $this->getFromRequest('id');
        if (!is_array($req_id))
            $req_id = array($req_id);
        $itemForDelete = array();
        if (!is_null($this->getFromRequest('id'))) {
            foreach ($req_id as $workOrderID) {
                //	Access control
                if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
                    throw new Exception('deny');
                }
                $workOrder = new WorkOrder($this->db, $workOrderID);
                $delete = array();
                $delete["id"] = $workOrder->id;
                $delete["number"] = $workOrder->number;
                $delete["description"] = $workOrder->description;
                $delete["customer_name"] = $workOrder->customer_name;
                $delete["status"] = $workOrder->status;
                $delete["facility_id"] = $workOrder->facility_id;
                $itemForDelete[] = $delete;
            }
        }
        if (!is_null($this->getFromRequest('facilityID'))) {
            $this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=" . $this->getFromRequest('facilityID') . "&bookmark=workOrder");
            //as ShowAddItem
            $params = array("bookmark" => "workOrder");

            $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
            $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
            $this->setPermissionsNew('viewFacility');
        }

        $this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
    }

    private function actionConfirmDelete() {

        foreach ($this->itemID as $ID) {

            $workOrder = new WorkOrder($this->db, $ID);
            $facilityId = $workOrder->facility_id;
            // get work order mix id, we check if work order already has any mixes
            $mixOptimized = new MixOptimized($this->db);
            $mixIDs = $workOrder->getMixes();
            if (count($mixIDs) < 2) {
                // we can delete only empty work order
                $workOrder->delete();
                // delete empty mix
                $mixOptimized = new MixOptimized($this->db, $woId);
                $mixOptimized->delete();
            } else {
                header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=workOrder&notify=49");
                die();
            }
        }

        if ($this->successDeleteInventories)
            header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=workOrder&notify=48");
    }

    protected function actionEdit() {

        //	Access control
        if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
            throw new Exception('deny');
        }
        $workOrder = new WorkOrder($this->db, $this->getFromRequest('id'));
        $this->smarty->assign('data', $workOrder);

        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $params = array("bookmark" => "workOrder");

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setPermissionsNew('viewWorkOrder');

        //	set js scripts
        $jsSources = array(
            'modules/js/reg_country_state.js',
            'modules/js/saveItem.js',
            'modules/js/PopupWindow.js',
            'modules/js/addJobberPopups.js',
            'modules/js/checkBoxes.js'
        );
        $this->smarty->assign('jsSources', $jsSources);

        $this->smarty->assign('tpl', 'tpls/addWorkOrder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

}

?>