<?php

namespace Cirici\ApiBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * AccessToken
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Cirici\ApiBundle\Entity\Client
     */
    protected $client;

    /**
     * @var \Cirici\ApiBundle\Entity\User
     */
    protected $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client
     *
     * @param \Cirici\ApiBundle\Entity\Client $client
     * @return AccessToken
     */
    public function setClient(\FOS\OAuthServerBundle\Model\ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Cirici\ApiBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set user
     *
     * @param \Cirici\ApiBundle\Entity\User $user
     * @return AccessToken
     */
    public function setUser(\Symfony\Component\Security\Core\User\UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Cirici\ApiBundle\Model\UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
