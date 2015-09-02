<?php

namespace Cirici\ApiBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User extends BaseUser
{
    /**
     * @var integer
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /* TO MOVE TO DavantisApiBundle */
    /**
     * @var \Cirici\DavantisApiBundle\Entity\Company
     */
    private $company;


    /**
     * Set company
     *
     * @param \Cirici\DavantisApiBundle\Entity\Company $company
     * @return User
     */
    public function setCompany(\Cirici\DavantisApiBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Cirici\DavantisApiBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}
