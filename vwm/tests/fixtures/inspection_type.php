<?php

return array(
  array('settings' => '{"typeName":"Test1","permit":0,"subtypes":[{"name":"NOT IN USE","notes":0,"qty":0,"valueGauge":0},{"name":"GOOD","notes":0,"qty":0,"valueGauge":0},{"name":"DAMAGED","notes":1,"qty":0,"valueGauge":0},{"name":"REPLACED","notes":1,"qty":0,"valueGauge":0}]}'),
  array('settings' => '{"typeName":"Test2","permit":0,"subtypes":[{"name":"NOT IN USE","notes":0,"qty":0,"valueGauge":0},{"name":"READING","notes":0,"qty":0,"valueGauge":1},{"name":"SERVICE","notes":1,"qty":0,"valueGauge":0}]}'),
  array('settings' => '{"typeName":"Test3","permit": 1,"subtypes": [{"name": "TEMPERATURE READING","notes": 0,"qty": 0,"valueGauge": 1,"gaugeType":0},{"name": "NOT IN USE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "REPLACEMENT","notes": 1,"qty": 0,"valueGauge": 0},{"name": "READING","notes": 0,"qty": 0,"valueGauge": 1},{"name": "SERVICE","notes": 1,"qty": 0,"valueGauge": 0}],"additionFieldList":[{"name":"Natural Gas","gaugeType":3},{"name":"Propane Gas","gaugeType":3},{"name":"Electric","gaugeType":4}]}'),
  array('settings' => '{"typeName":"Test4","permit":1,"subtypes":[{"name":"TEMPERATURE READING","notes":0,"qty":0,"valueGauge":1},{"name":"NOT IN USE","notes":0,"qty":0,"valueGauge":0},{"name":"REPLACEMENT","notes":1,"qty":0,"valueGauge":0},{"name":"READING","notes":0,"qty":0,"valueGauge":1},{"name":"SERVICE","notes":1,"qty":0,"valueGauge":0}],"additionFieldList":[{"name":"Natural Gas","gaugeType":3},{"name":"Propane Gas","gaugeType":3},{"name":"Electric","gaugeType":4}]}'),
  array('settings' => '{"typeName":"Test5","permit": 1,"subtypes": [{"name": "NOT IN USE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "READING","notes": 0,"qty": 0,"valueGauge": 1,"gaugeType":2},{"name": "SERVICE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "WASTE","notes": 0,"qty": 1,"valueGauge": 0}]}'),
);
?>
