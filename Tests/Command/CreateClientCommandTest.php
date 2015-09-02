<?php

namespace Cirici\ApiBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Cirici\ApiBundle\Command\CreateClientCommand;

class CreateClientCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new CreateClientCommand());

        $command = $application->find('cirici:oauth-server:client:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                '--redirect-uri'    => array('127.0.0.1'),
                '--grant-type'    => array(
                    'authorization_code',
                    'password',
                    'refresh_token',
                    'client_credentials',
                ),
            )
        );

        $this->assertRegExp('/Added a new client with public id/', $commandTester->getDisplay());
    }
}
