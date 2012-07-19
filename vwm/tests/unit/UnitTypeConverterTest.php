<?php

class UnitTypeConverterTest extends TestCase {

	public function testConvertDensity(){
		$value = 3;

		//	gram per liter
		$from = new Density($this->db);
		$from->setNumerator(Unittype::UNIT_G_ID);
		$from->setDenominator(Unittype::UNIT_L_ID);

		// lbs per gallon
		$to = new Density($this->db);
		$to->setNumerator(Unittype::UNIT_LBS_ID);
		$to->setDenominator(Unittype::UNIT_GAL_ID);

		$converter = new UnitTypeConverter();

		$result = $converter->convertDensity($value, $from, $to, new Unittype($this->db));
		//$this->assertTrue(is_float($result));
		//$this->assertTrue(round($result, 3) == 0.025);
	}
}