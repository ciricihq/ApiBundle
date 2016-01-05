<?php

namespace Cirici\ApiBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Cirici\ApiBundle\Command\ExpireTokenCommand;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

class ExpireTokenCommandTest extends KernelTestCase
{
    public function setUp()
    {
        self::bootKernel();

        $this->addMappings("Cirici\ApiBundle\Tests\Entity");
        $this->addMappings("Cirici\ApiBundle\Entity");
    }

    public function testExecute()
    {
        $application = new Application(static::$kernel);
        $application->add(new ExpireTokenCommand());

        $command = $application->find('cirici:oauth-server:token:expire');
        $commandTester = new CommandTester($command);
        $commandTester->execute( array(
            '--token' => 1
        ) );

        // @todo Create fixture token from here to test invalidated
        // $this->assertRegExp('/has been expired/', $commandTester->getDisplay());
    }

    public function addMappings($namespace)
    {
        $emanager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $configuration  = $emanager->getConfiguration();
        $annotationDriver = new AnnotationDriver(
            static::$kernel->getContainer()->get('annotation_reader'),
            [__DIR__ . '/../Entity']
        );

        /** @var MappingDriverChain $driver */
        $driver = $configuration->getMetadataDriverImpl();
        $driver->addDriver($annotationDriver, $namespace);
        $configuration->addEntityNamespace('CiriciApiBundle', $namespace);
    }
}
