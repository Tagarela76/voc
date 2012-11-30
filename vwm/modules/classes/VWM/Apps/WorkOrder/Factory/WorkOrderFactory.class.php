<?php
namespace VWM\Apps\WorkOrder\Factory;
use VWM\Apps\WorkOrder\Entity\IndustrialWorkOrder;
use VWM\Apps\WorkOrder\Entity\AutomotiveWorkOrder;

class WorkOrderFactory {
      
    static function createWorkOrder($db, $industryTypeId, $id=null) { 
        
        switch ($industryTypeId) {
            
            case "3" :
                // INDUSTRIAL
                $workOrder = new IndustrialWorkOrder($db, $id);
                return $workOrder;
            break;   
        
            case "5" :
                // AUTOMOTIVE
                $workOrder = new AutomotiveWorkOrder($db, $id);
                return $workOrder;
            break; 
        
            default : 
                // AUTOMOTIVE
                $workOrder = new AutomotiveWorkOrder($db, $id);
                return $workOrder;
            break;    
        }
    }
    //WorkOrderFactory::createWorkOrder($db, $industryTypeId)
}
?>
