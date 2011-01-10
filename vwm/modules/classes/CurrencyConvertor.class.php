<?php
/**
 * 
 * @author developer Ilya
 *
 */
class CurrencyConvertor
{
	private $currencies;
	
	function CurrencyConvertor()
	{
		try
		{
			$xml = new SimpleXMLElement("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml",0,true);
		}
		catch(Exception $e)
		{
			throw $e;
		}
		
		$time = $xml->Cube->Cube['time'];
		//echo $time;
		
		//var_dump($xml->Cube->Cube->Cube);
		
		$this->currencies = array();
		foreach($xml->Cube->Cube->Cube as $i)
		{
			//echo $i['currency'] . " - " . $i['rate'] . "<br/>"; 
			
			$this->currencies["{$i['currency']}"] = floatval($i['rate']);
		}
		
		/*foreach($XMLContent as $line) {
			
	        if(preg_match("/currency='([[:alpha:]]+)'/",$line,$currencyCode)){
	            if(preg_match("/rate='([[:graph:]]+)'/",$line,$rate)){
	                
	            	$this->currencies[$currencyCode[1]] = $rate[1];
	            }
	        }
		}*/
		$this->currencies["EUR"] = floatval(1.0);
	}
	
	/**
	 * Calculate sum of differents currencies and return result in return type of currency (USD for example)
	 * @param $valuts array ('USD' => 100.0, 'EUR' => 123.0)
	 * @param $returnType string ISO code of currency (USD,EUR,GBP etc). "USD" is <b>default</b>
	 * @return sum of money or false if data incorrect
	 */
	public function Sum($valuts,$returnType = "USD")
	{
		if( empty($valuts))
		{
			throw new Exception("Valuts is empty!");
		}
		elseif(!is_array($valuts)){
			
			throw new Exception("valuts is not array!");
		}
		elseif(empty($this->currencies[$returnType])){
			
			throw new Exception("Return ISO type is empty or does not exists in currencies list!. ISO type: $returnType");
		}
		
		/*Convert money to Euro*/
		$eurosTotalSum = 0.0;
		$keys = array_keys($valuts);
		foreach($keys as $key)
		{
			if(isset($this->currencies[$key])) //Currency is exists in our currencies list
			{
				$value = $valuts[$key];
				$factor = $this->currencies[$key];
				$eurosTotalSum += $value / $factor;
			}
		}
		
		/*Convert euro total sum to return currency type (USD default)*/
		$factor = $this->currencies[$returnType];
		$totalSum = $eurosTotalSum * $factor;
		return $totalSum;
	}
}


?>