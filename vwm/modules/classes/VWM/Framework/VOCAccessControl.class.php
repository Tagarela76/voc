<?php

namespace VWM\Framework;

require_once(site_path.'modules/phpgacl/gacl.class.php');
require_once(site_path.'modules/phpgacl/gacl_api.class.php');

class VOCAccessControl {

	/**
	 * @var \gacl_api
	 */
	private $gaclApi;

	const ARO = 'ARO';

	public function __construct() {
		$this->gaclApi = new \gacl_api();
	}

	/**
	 * Adds user to group. Common case - add new user to department
	 * @param string $userAccessName
	 * @param string $groupName consists from accesslevel string plus id
	 * separated by "_". For example, "department_12", "facility_99", "root"
	 * @return boolean true if successful, false otherwise
	 */
	public function addUserToGroup($userAccessName, $groupName) {
		$groupID = $this->getGroupIdByName($groupName);
		if(!$groupID) {
			return false;
		}

		return $this->gaclApi->add_group_object($groupID, 'users', $userAccessName, self::ARO);
	}

	/**
	 * Gets the group_id given the name or value.
	 * Will only return one group id, so if there are duplicate names,
	 * it will return false.
	 * @param string $groupName
	 * @return int|bool Returns Group ID if found and Group ID is unique in
	 * database, otherwise, returns false
	 */
	public function getGroupIdByName($groupName) {
		//	yes, they are equal
		$aroGroupName = $aroGroupValue = $groupName;

		return $this->gaclApi->get_group_id($aroGroupName, $aroGroupValue, self::ARO);
	}

	/**
	 * @param string $groupName
	 * @return array
	 */
	public function getGroupUsers($groupName) {
		$users = $this->gaclApi->get_group_objects($this->getGroupIdByName($groupName));
		return $users['users'];
	}

	/**
	 * @param string $groupName
	 * @return boolean
	 */
	public function removeAllUsersFromGroup($groupName) {
		$groupId = $this->getGroupIdByName($groupName);
		if(!$groupId) {
			return false;
		}
		$users = $this->gaclApi->get_group_objects($groupId);
		foreach ($users['users'] as $user) {
			$this->gaclApi->del_group_object($groupId, 'users', $user);
		}
		return true;
	}


	public function getUserGroups($accessName) {
		$aroId = $this->gaclApi->get_object_id('users', $accessName, self::ARO);
		$groupIds = $this->gaclApi->get_object_groups($aroId);
		$groups = array();
		foreach ($groupIds as $groupId) {
			$groupDetails = $this->gaclApi->get_group_data($groupId);
			$groups[] = $groupDetails[2];
		}

		return $groups;
	}
}

?>
