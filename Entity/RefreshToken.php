<?php

namespace Cirici\ApiBundle\Entity;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * RefreshToken
 *
 * @ORM\Entity
 */
class RefreshToken extends BaseRefreshToken
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Cirici\ApiBundle\Entity\Client
     *
     * @ORM\ManyToOne(targetEntity="Cirici\ApiBundle\Entity\Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @var \Cirici\ApiBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Cirici\ApiBundle\Model\UserInterface")
     * @ORM\JoinColumn(onDelete="CASCADE")
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
     * @return RefreshToken
     */
    public function setClient(\FOS\OAuthServerBundle\Model\ClientInterface $client = null)
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
     * @return RefreshToken
     */
    public function setUser(\Symfony\Component\Security\Core\User\UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Cirici\ApiBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
