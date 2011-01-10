<?php
	$fileLocation = "/etc/php5/apache2/php.ini";
	
	$f = fopen($fileLocation, 'r');
	
	while(!feof($f)) {
		$line = fgets($f);
		echo $line."<br>";
	}
	
	fclose($f);
?>
