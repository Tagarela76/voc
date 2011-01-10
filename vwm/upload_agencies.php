<?php
/**
Upload agencies into agency table 
**/

require_once('config/constants.php');
require_once('modules/classAutoloader.php');
require_once('modules/xnyo/startXnyo.php');

class CSVUploader
{
	public function LoadCSV($filename)
	{
		$file = fopen($filename,"r") or die("cannot open the $filename!");
		$fsize = filesize($filename);
		
		$agencies;
		
		try
		{ 	
			while (($row = fgetcsv($file, 1000, ",")) !== FALSE)
			{
				if(count($row) == 4)
				{
					if($row[0] != '' && $row[1] != '')
					{
						
						$r["name_us"] = trim( mysql_escape_string($row[0]));
						$r["description"] = trim(mysql_escape_string($row[1]));
						$r["location"] = trim(mysql_escape_string($row[2]));
						$r["contact_info"] = trim(mysql_escape_string($row[3]));
						
						$agencies[] = $r;
					}
				}
				
				
			}
		}catch (Exception $e) {
	    	echo 'Caught exception: '.  $e->getMessage(). " : ".error_get_last().  "<br/>";
		}
		fclose($file);
		return $agencies;
	}
	
	public function InsertAgenciesIntoDB($db,$agencies)
	{
		$query = "insert into ". TB_AGENCY . "(name_us,description,country_id,location,contact_info) values ";
		
		if(count($agencies) == 0)
		{
			return null;
		}
		
		foreach($agencies as $a)
		{
			$query .= "(\"{$a['name_us']}\",\"{$a['description']}\",215,\"{$a['location']}\",\"{$a['contact_info']}\"),";
		}
		
		$query = substr_replace($query,";",strlen($query)-1);
		
		$db->query($query);
	}
}

	if( $xnyo->load_sql() )
	{
		$db->select_db(DB_NAME) or die('Database connection fail!');
		
		//$db->query("select * from country");
		//$arr = $db->fetch_all_array();
		//var_dump($arr);
	}
	
	$csv = new CSVUploader();
	
	
	
	if($_POST['btnUploadFile'])
	{
		
		$filename = $_FILES['odsFile']['name'];
		
		move_uploaded_file($_FILES['odsFile']['tmp_name'],$filename);
		
		$agencies = $csv->LoadCSV($filename);
		
		$csv->InsertAgenciesIntoDB($db,$agencies);
		
		echo "Uploaded!";
	}
?>

<html>
	<head>
		<title>Upload Agencies</title>
		<style type="text/css">
			input
			{
				padding:5px;
				margin:5px;
			}
		</style>
	</head>
	<body>
	<div style="width:100%;text-align:center;">
		<form method="POST" enctype="multipart/form-data" name="uploadOdsFile">
			<h3>Select *.CSV file:</h3>
			<input type="file" name="odsFile"/><br/>
			<input type="submit" value="Upload File" name="btnUploadFile" />
		</form>
	</div>	
	</body>
</html>