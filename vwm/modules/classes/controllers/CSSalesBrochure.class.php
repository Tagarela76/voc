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

}

?>