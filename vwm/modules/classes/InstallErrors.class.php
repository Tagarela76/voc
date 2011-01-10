<?php
class InstallErrors
{
	private function formatString ($str, $limit=85)
	{
		$str= str_replace("<br>"," <br> ",$str);
		$strArr=split(" ",$str);
		$resultStr="";
		$lineLength=0;
		
		foreach ($strArr as $substr)
		{
			if ($substr=="<br>")
			{
				$resultStr.="<br>";
				$lineLength=0;
			}
			else
			{
				$str_len=strlen($substr);
				if (($str_len+$lineLength)<$limit)
				{
					$resultStr.=$substr." ";
					$lineLength+=$str_len+1;
				}
				else
				{
					$resultStr.="<br>" .$substr." ";
					$lineLength=strlen($substr);
				}
			}
		}		
		return $resultStr;
	}
	
	public function folderPermissionsError()
	{
		$errStr="To correct this error you must set permissions on a create and delete to this folder and her subfolders. " .
				"Set permissions on a read and write to files of this folder";
		return $this->formatString($errStr);
	}	
	
	public function filePermissionsError()
	{
		$errStr="To correct this error you must set permissions on a read and write to this file";
		return $this->formatString($errStr);
	}
	
	public function mysqlError()
	{
		$errStr="To correct this error you must install MySQL Server. Find the file of php.ini and " .
				"will set next options: 	<br><br>".
				"extension_dir = \"your extednsion dir\" for example \"C:/php/ext\"	 <br>".
				"extension=php_mysql.dll	 <br>".
				"extension=php_mysqli.dll	 <br>".
				"mysql.allow_persistent =	On	 <br>".
				"mysql.max_persistent =	-1	 <br>".
				"mysql.max_links = 		-1	 <br>".
				"mysql.default_socket =	NULL <br>".
				"mysql.default_port	=	NULL <br>".
				"mysql.default_host	=	NULL <br>".
				"mysql.default_user	=	NULL <br>".
				"mysql.default_password = NULL <br>".
				"mysql.connect_timeout =	0	 <br>";				

		return $this->formatString($errStr);
	}	
	
	public function sessionError()
	{
		$errStr="To correct this error you must find the file of php.ini and " .
				"will set next options: 	<br><br>".
				"session.save_handler = files	 <br>".
				"session.use_cookies = 1	 <br>".
				"session.use_only_cookies = 1 <br>".
				"session.name = PHPSESSID <br>".
				"session.auto_start = 0      <br>".
				"session.cookie_lifetime = 0 <br>".
				"session.cookie_path = / <br>".
				"session.serialize_handler = php <br>".
				"session.gc_probability = 1	 <br>".	
				"session.gc_divisor = 1000	 <br>".	
				"session.gc_maxlifetime = 1440	 <br>".	
				"session.bug_compat_42 = Off	 <br>".	
				"session.bug_compat_warn = Off	 <br>".	
				"session.entropy_length = 0	 <br>".	
				"session.cache_limiter = nocache	 <br>".	
				"session.cache_expire = 180	 <br>".
				"session.use_trans_sid = 0	 <br>".	
				"session.hash_bits_per_character = 5	 <br>".	
				"url_rewriter.tags = \"a=href,area=href,frame=src,input=src,form=fakeentry\"";	
								

		return $this->formatString($errStr);
	}	
	
	public function xmlError()
	{
		$errStr="Install the xml extension";				

		return $this->formatString($errStr);
	}	
	
	public function zendError()
	{
		$errStr="To correct this error you must install Zend Optimizer version 3.3.3 or latest.";			

		return $this->formatString($errStr);
	}
	
	public function jsonError()
	{
		$errStr="Install the json extension";			

		return $this->formatString($errStr);
	}	
	
	public function mbstringError()	
	{	
		$errStr="To correct this error you must find the file of php.ini and " .
				"will set next options: 	<br><br>".			
				"extension_dir = \"your extednsion dir\" for example \"C:/php/ext\"	 <br>".
				"extension=php_mbstring.dll	 <br>";
				
		return $this->formatString($errStr);
	}	
	public function phpVariablesError()
	{
		$errStr="Error";			

		return $this->formatString($errStr);
	}	
}
?>
