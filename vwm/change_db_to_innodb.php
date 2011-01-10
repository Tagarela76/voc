<?php
/**
Change all database tables from MyISAM to InnoDB type
**/

require_once('config/constants.php');
require_once('modules/classAutoloader.php');
require_once('modules/xnyo/startXnyo.php');

function println($var)
{
	echo $var . "<br/>";
}

if( $xnyo->load_sql() )
{
	$db->select_db(DB_NAME) or die('Database connection fail!');
	
	
	$db->query("select table_name, engine, TABLE_COLLATION, table_schema from information_schema.tables 
		where engine = 'MyISAM' and table_schema = 'voc' ");
	$arr = $db->fetch_all_array();
	
	$count = count($arr);
	println("Total: " . $count);

	println("Changing tables from MyISAM to InnoDB type");

	for($i=0; $i<$count; $i++)
	{
		$db->query("alter table " . $arr[$i]['table_name'] . " engine = InnoDB");
		println($i.". " . $arr[$i]['table_name'] . " changed");
	}
}

?>
