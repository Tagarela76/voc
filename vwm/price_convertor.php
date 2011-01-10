<?php
/*
 * Created on Dec 30, 2010
 * 
 * Convert all prices in modules, billing and limits into other formats and save in db
 */			
	
define ('DIRSEP', DIRECTORY_SEPARATOR);
$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP; 
define ('site_path', $site_path);

require_once('config/constants.php');
require_once('modules/classAutoloader.php');
require ('modules/xnyo/startXnyo.php'); 
$db->select_db(DB_NAME);

//Cause curancies in CurrencyConvector is private we should use class CurrencyConvertor for all changes!
$query = "SELECT * FROM ".TB_VPS_CURRENCY." ";
$db->query($query);
$cdata = $db->fetch_all_array();
$currencies = array();
foreach($cdata as $currency) {
	if ($currency['iso'] == 'USD') {
		$usd = $currency['id'];
	} else {
		$currencies[$currency['id']] = $currency['iso'];
	}
}

$currencyConvertor = new CurrencyConvertor();

//convert Billing2Currency
$query = "SELECT * FROM ".TB_VPS_BILLING2CURRENCY."  WHERE currency_id = '".$usd."' ";
$db->query($query);
$billingData = $db->fetch_all_array();

$insertQuery = "INSERT INTO ".TB_VPS_BILLING2CURRENCY." (billing_id, currency_id, price, one_time_charge) VALUES ";
$removeQuery = "DELETE FROM ".TB_VPS_BILLING2CURRENCY." WHERE currency_id != '$usd' ";
foreach($billingData as $data) {
	$valuts = array	(array('USD' => $data['price']),
		array('USD' => $data['one_time_charge']));
	foreach($currencies as $id => $iso) {
		$insertQuery .= "('".$data['billing_id']."', '".$id."', '".
			round($currencyConvertor->Sum($valuts[0],$iso),2, PHP_ROUND_HALF_UP)."', '".round($currencyConvertor->Sum($valuts[1],$iso),2, PHP_ROUND_HALF_UP)."'), ";
	}
}
$query = substr($insertQuery,0,-2);

$db->query($removeQuery);
$db->query($query);

//convert Module2Currency
$query = "SELECT * FROM ".TB_VPS_MODULE2CURRENCY."  WHERE currency_id = '$usd' ";
$db->query($query);
$moduleData = $db->fetch_all_array();

$insertQuery = "INSERT INTO ".TB_VPS_MODULE2CURRENCY." (module_billing_id, currency_id, price) VALUES ";
$removeQuery = "DELETE FROM ".TB_VPS_MODULE2CURRENCY." WHERE currency_id != '$usd' ";

foreach($moduleData as $data) {
	$valuts = array	('USD' => $data['price']);
	foreach($currencies as $id => $iso) {
		$insertQuery .= "('".$data['module_billing_id']."', '".$id."', '".
			round($currencyConvertor->Sum($valuts,$iso),2, PHP_ROUND_HALF_UP)."'), ";
	}
}
$query = substr($insertQuery,0,-2);

$db->query($removeQuery);
$db->query($query);


//convert Limit_Price2Currency
$query = "SELECT * FROM ".TB_VPS_LIMIT_PRICE2CURRENCY." WHERE currency_id = '$usd' ";
$db->query($query);
$moduleData = $db->fetch_all_array();

$insertQuery = "INSERT INTO ".TB_VPS_LIMIT_PRICE2CURRENCY." (vps_limit_price_id, currency_id, increase_cost) VALUES ";
$removeQuery = "DELETE FROM ".TB_VPS_LIMIT_PRICE2CURRENCY." WHERE currency_id != '$usd' ";

foreach($moduleData as $data) {
	$valuts = array	('USD' => $data['increase_cost']);
	foreach($currencies as $id => $iso) {
		$insertQuery .= "('".$data['vps_limit_price_id']."', '".$id."', '".
			round($currencyConvertor->Sum($valuts,$iso),2, PHP_ROUND_HALF_UP)."'), ";
	}
}
$query = substr($insertQuery,0,-2);

$db->query($removeQuery);
$db->query($query);

echo "Done!";
//Done!)
?>