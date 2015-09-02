<?php

namespace Cirici\ApiBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Cirici\ApiBundle\Command\ExpireTokenCommand;

class ExpireTokenCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new ExpireTokenCommand());

        $command = $application->find('cirici:oauth-server:token:expire');
        $commandTester = new CommandTester($command);
        $commandTester->execute( array(
            'token' => 1
        ) );

        // @todo Create fixture token from here to test invalidated
        // $this->assertRegExp('/has been expired/', $commandTester->getDisplay());
    }
}
