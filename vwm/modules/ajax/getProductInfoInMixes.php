<?php
if(isset($_GET['product_id'])){
	chdir('../..');

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');

	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);

	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');


	//	Start xnyo Framework
	require ('modules/xnyo/startXnyo.php');

	$xnyo->filter_get_var('product_id', 'int');


	$db->select_db(DB_NAME);
	$query = 'SELECT * FROM '.TB_PRODUCT.' WHERE product_id = \''.$_GET['product_id'].'\'';

	$db->query($query);

	if ($db->num_rows() == 1) {
		$data = $db->fetch(0);
		
		$description = $data->name;

		$query = "SELECT * FROM ".TB_COAT." WHERE coat_id=".$data->coating_id;
		$db->query($query);
		$coatName = $db->fetch(0)->coat_desc;

		try {
			$intDentisy = intval($data->density);

			if($intDentisy < 1) {
				$isSupportWeight = false;
			} else {
				$isSupportWeight = true;
			}

		}catch(Exception $e) {
			$isSupportWeight = false;
		}

		$response = json_encode(Array("description" => $description, "coatName" => $coatName, "supportWeight" => $isSupportWeight));



		echo $response;
	} else
		echo 'false';
}
?>