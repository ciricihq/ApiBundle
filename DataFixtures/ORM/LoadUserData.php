<?php

namespace Cirici\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $usermanager = $this->container->get('fos_user.user_manager');

        $user = $usermanager->createUser();
        $user->setUsername('testuser');
        $user->setEmail('testuser@test.com');
        $user->setEnabled(true);
        $user->setPlainPassword('test');
        $user->addRole('ROLE_USER');
        $usermanager->updateUser($user);

        $this->addReference('testuser', $user);
    }

    public function getOrder()
    {
        return 1;
    }
}
