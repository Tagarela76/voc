<?php

namespace VWM\Apps\User\Entity;

use VWM\Framework\Model;

class User extends Model
{

    protected $user_id;
    protected $username;
    protected $accessname;
    protected $password;
    protected $phone;
    protected $mobile;
    protected $email;
    protected $accesslevel_id;
    protected $company_id;
    protected $facility_id;
    protected $department_id;
    protected $grace;
    protected $creater_id;
    protected $terms_conditions;

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getAccessname()
    {
        return $this->accessname;
    }

    public function setAccessname($accessname)
    {
        $this->accessname = $accessname;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getMobile()
    {
        return $this->mobile;
    }

    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getAccesslevelId()
    {
        return $this->accesslevel_id;
    }

    public function setAccesslevelId($accesslevelId)
    {
        $this->accesslevel_id = $accesslevelId;
    }

    public function getCompanyId()
    {
        return $this->company_id;
    }

    public function setCompanyId($companyId)
    {
        $this->company_id = $companyId;
    }

    public function getFacilityId()
    {
        return $this->facility_id;
    }

    public function setFacilityId($facilityId)
    {
        $this->facility_id = $facilityId;
    }

    public function getDepartmentId()
    {
        return $this->department_id;
    }

    public function setDepartmentId($departmentId)
    {
        $this->department_id = $departmentId;
    }

    public function getGrace()
    {
        return $this->grace;
    }

    public function setGrace($grace)
    {
        $this->grace = $grace;
    }

    public function getCreaterId()
    {
        return $this->creater_id;
    }

    public function setCreaterId($createrId)
    {
        $this->creater_id = $createrId;
    }

    public function getTermsConditions()
    {
        return $this->terms_conditions;
    }

    public function setTermsConditions($termsConditions)
    {
        $this->terms_conditions = $termsConditions;
    }

    public function getAttributes()
    {
        
    }
}
?>
