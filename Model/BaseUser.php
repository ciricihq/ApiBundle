<?php

namespace Cirici\ApiBundle\Model;

use FOS\UserBundle\Model\User as FOSBaseUser;
use Cirici\ApiBundle\Entity\User;

abstract class BaseUser extends User
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
}
