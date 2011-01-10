<?php
	//echo "{
 	//	query:'Li',
 	//	suggestions:['Liberia','Libyan Arab Jamahiriya','Liechtenstein','Lithuania'],
 	//	data:['LR','LY','LI','LT']
	//	}";
	$response = array('query'=>$_GET['query'], 'suggestions'=>array('Liberia', 'Libyan Arab Jamahiriya'));
	echo json_encode($response);
?>

