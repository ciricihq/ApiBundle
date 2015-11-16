<?php
namespace Cirici\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExpireTokenCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cirici:oauth-server:token:expire')
            ->setDescription('Force expire of token')
            ->addOption(
                'token_id',
                null,
                InputOption::VALUE_OPTIONAL,
                'The token internal id'
            )
            ->addOption(
                'token',
                null,
                InputOption::VALUE_OPTIONAL,
                'The token to expire'
            )
            ->setHelp(
                <<<EOT
                    The <info>%command.name%</info>command forces token to expire.

<info>php %command.full_name% token_id</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $token = null;

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        if ($input->getOption('token_id')) {
            $token = $em->getRepository('CiriciApiBundle:AccessToken')->findOneById($input->getOption('token_id'));
        }

        if ($input->getOption('token')) {
            $token = $em->getRepository('CiriciApiBundle:AccessToken')->findOneByToken($input->getOption('token'));
        }

        if ($token) {
            $token->setExpiresAt($token->getExpiresAt() - 4000);
            $em->persist($token);
            $em->flush($token);

            if ($token->hasExpired()) {
                $output->writeln("The token has been expired");
            } else {
                $output->writeln("The token hasn't been expired");
            }
        } else {
            $output->writeln("The token cannot be found");
        }
    }
}
