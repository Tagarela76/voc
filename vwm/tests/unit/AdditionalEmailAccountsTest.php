<?php

use VWM\Framework\Test as Testing;

class AdditionalEmailAccountsTest extends Testing\DbTestCase {

	protected $fixtures = array(
		TB_COMPANY, TB_FACILITY, TB_DEPARTMENT, TB_ADDITIONAL_EMAIL_ACCOUNTS, TB_USER
	);

	public function testAdditionalEmailAccounts() {
		$additionalEmailAccounts = new AdditionalEmailAccounts($this->db);
		$this->assertTrue($additionalEmailAccounts instanceof AdditionalEmailAccounts);
		$this->assertTrue(!is_null($additionalEmailAccounts));
	}

	public function testAddAdditionalEmailAccounts() {

		$additionalEmailAccounts = new AdditionalEmailAccounts($this->db);
		$additionalEmailAccounts->username = 'user_5';
		$additionalEmailAccounts->email = 'user_5@mail.ru';
		$additionalEmailAccounts->company_id = "125";
		$additionalEmailAccounts->save();

		$myTestAdditionalEmailAccount = Phactory::get(TB_ADDITIONAL_EMAIL_ACCOUNTS, array('email'=>"user_5@mail.ru"));
		$this->assertTrue($myTestAdditionalEmailAccount->username == 'user_5');

		$additionalEmailAccounts = new AdditionalEmailAccounts($this->db);
		$additionalEmailAccounts->username = 'user_5';
		$additionalEmailAccounts->email = 'test1@mail.ru';
		$additionalEmailAccounts->company_id = "125";
		$additionalEmailAccountId = $additionalEmailAccounts->save();
		$this->assertTrue($additionalEmailAccountId == false);

		// check if we can add additional email account if entered email is already in use (in User Table)
		$additionalEmailAccounts = new AdditionalEmailAccounts($this->db);
		$additionalEmailAccounts->username = 'user_5';
		$additionalEmailAccounts->email = '2reckiy@mail.ru';
		$additionalEmailAccounts->company_id = "125";
		$additionalEmailAccountId = $additionalEmailAccounts->save();
		$this->assertTrue($additionalEmailAccountId == false);

	}

	public function testDeleteAdditionalEmailAccounts() {

		$additionalEmailAccounts = new AdditionalEmailAccounts($this->db, '1');
		$additionalEmailAccounts->delete();

		$myTestAdditionalEmailAccount = Phactory::get(TB_ADDITIONAL_EMAIL_ACCOUNTS, array('email'=>"test1@mail.ru"));
		$this->assertTrue(is_null($myTestAdditionalEmailAccount));
	}

	public function testGetAdditionalEmailAccountsByCompany() {

		$additionalEmailAccounts = new AdditionalEmailAccounts($this->db);
		$additionalEmailAccountsList = $additionalEmailAccounts->getAdditionalEmailAccountsByCompany(125);

		$this->assertTrue(is_array($additionalEmailAccountsList));

		$this->assertTrue(count($additionalEmailAccountsList) == 4);

		$this->assertTrue($additionalEmailAccountsList[0] instanceof AdditionalEmailAccounts);

		$myTestAdditionalEmailAccount = Phactory::get(TB_ADDITIONAL_EMAIL_ACCOUNTS, array('email'=>"test1@mail.ru"));
		$this->assertTrue($myTestAdditionalEmailAccount->username == 'test1');
	}

}