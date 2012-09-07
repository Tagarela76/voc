<?php

class CSSalesBrochure extends Controller {

    function CSSalesBrochure($smarty, $xnyo, $db, $user, $action) {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'SalesBrochure';
    }

    function runAction() {

        $this->runCommon();
        $functionName = 'action' . ucfirst($this->action);
        if (method_exists($this, $functionName))
            $this->$functionName();
    }

    private function actionUpdateItem() {

        $request = $this->getFromRequest();
		$salesBrochure = new SalesBrochure($this->db, "1");
        $salesBrochure->title_up = $request["salesBrochureTitleUp"];
        $salesBrochure->title_down = $request['salesBrochureTitleDown'];
        $salesBrochure->sales_client_id = $request['salesBrochureClientId'];
		$salesBrochure->save();

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

	private function actionCreateLabel() {
		
		$workOrder = new WorkOrder($this->db, $this->getFromRequest('id'));
		$mixList = array();
        // get child mixes 
		$MixTotalPrice = 0;
        $mixes = $workOrder->getMixes();
		foreach ($mixes as $mix) {
			$mixOptimized = new MixOptimized($this->db, $mix->mix_id);
			$mix->price = $mixOptimized->getMixPrice();
			$MixTotalPrice += $mix->price;
			$mixList[] = $mix;
		}
		$workOrder->totalPrice = $MixTotalPrice;
        $this->smarty->assign('workOrder', $workOrder);
        $this->smarty->assign('mixList', $mixList);

		$this->smarty->display("tpls/workOrderLabel.tpl");
	}

}

?>