<?php

namespace Cirici\ApiBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Cirici\ApiBundle\Command\ExpireTokenCommand;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

class BaseApiTestCase extends WebTestCase
{
    public $api_client;
    public $public_id;
    public $secret;

    private function addMappings($namespace)
    {
        $emanager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $configuration  = $emanager->getConfiguration();
        $annotationDriver = new AnnotationDriver(
            $this->getContainer()->get('annotation_reader'),
            [__DIR__ . '/../Entity']
        );

        /** @var MappingDriverChain $driver */
        $driver = $configuration->getMetadataDriverImpl();
        $driver->addDriver($annotationDriver, $namespace);
        $configuration->addEntityNamespace('CiriciApiBundle', $namespace);
    }

    public function setUp()
    {
        $this->addMappings("Cirici\ApiBundle\Tests\Entity");
        $this->addMappings("Cirici\ApiBundle\Entity");
        $this->loadFixtures(array());

        $this->loadFixtures(
            array(
                '\Cirici\ApiBundle\DataFixtures\ORM\LoadUserData',
            )
        );

        // Create testing client
        $this->api_client = $this->createTestingApiClient();
        $this->public_id = $this->api_client->getPublicId();
        $this->secret = $this->api_client->getSecret();
    }

    public function createTestingApiClient()
    {
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('users'));
        $client->setAllowedGrantTypes(array(
            'authorization_code',
            'password',
            'refresh_token',
            'token',
            'client_credentials',
        ));
        $clientManager->updateClient($client);

        return $client;
    }


    public function invalidateToken($token)
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new ExpireTokenCommand());

        $command = $application->find('cirici:oauth-server:token:expire');
        $commandTester = new CommandTester($command);
        $commandTester->execute( array(
            'token' => $token
        ) );
    }

    public function getAccessToken($client, $username = 'testuser', $password = 'test')
    {
        $api_client = $this->api_client;
        // Get the token
        $url = "/oauth/v2/token";
        // We can do this with post or get but in real app we'll do with post
        // or header, so let's try it!!

        $client->request('POST', $url, array(
            'client_id' => $api_client->getPublicId(),
            'client_secret' => $api_client->getSecret(),
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
        ));

        $token_values = json_decode($client->getResponse()->getContent(), true);

        return $token_values;
    }

    public function generateHeaders($token_values)
    {
        $headers = array(
            'HTTP_AUTHORIZATION' => "Bearer {$token_values['access_token']}",
            'CONTENT_TYPE' => 'application/json',
        );

        return $headers;
    }
}
