<?php

namespace Cirici\ApiBundle\Entity;

use Cirici\ApiBundle\Model\UserInterface;
use Doctrine\ORM\Mapping as ORM;

use FOS\UserBundle\Entity\User as BaseUser;

/**
 * User
 *
 * @implements UserInterface to allow extend the bundle on your app
 */
class User extends BaseUser implements UserInterface
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
