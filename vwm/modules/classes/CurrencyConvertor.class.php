<?php

class CurrencyConvertor
{
	private $currencies;

	function CurrencyConvertor()
	{

        $filename = date("d_m_Y")."_currency.xml";
        $tmpdir = "/tmp/";
        $fullfilenpath = getcwd().$tmpdir.$filename;

        if(!is_file($fullfilenpath)) {
            $res = copy("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml",$fullfilenpath);
            if($res) {

            } else {

            }
        }

		try
		{
			$xml = new SimpleXMLElement($fullfilenpath,0,true);
		}
		catch(Exception $e)
		{
			throw $e;
		}

		$time = $xml->Cube->Cube['time'];


		$this->currencies = array();
		foreach($xml->Cube->Cube->Cube as $i)
		{


			$this->currencies["{$i['currency']}"] = floatval($i['rate']);
		}

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