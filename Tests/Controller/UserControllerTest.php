<?php

namespace Cirici\ApiBundle\Tests\Controller;

class UserControllerTest extends BaseApiTestCase
{
    public function testUserRequestPassword()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        $headers = $this->generateHeaders($this->getAccessToken($client));
        $client->enableProfiler();
        $crawler = $client->request('POST', '/api/resetting/send-email', array(
            'username' => 'testuser@test.com'
        ), array(), $headers);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        // Check that an e-mail ws sent
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('Reset Password', $message->getSubject());
        // $this->assertEquals('send@example.com', key($message->getFrom()));
        $this->assertEquals('testuser@test.com', key($message->getTo()));
        $this->assertRegExp('/To reset your password - please visit/', $message->getBody());

        preg_match('@api/resetting/reset/(.[^\s]+)@', $message->getBody(), $change_password_url);
        $resetting_url = $change_password_url[1];

        $this->assertRegExp('/{"message":"email.sent"}/', $client->getResponse()->getContent(), "Password request not worked");

        // Check if the respense contents are json
        $this->assertJson($client);

        // Test again to check the message that you have already asked for password reset
        $crawler = $client->request('POST', '/api/resetting/send-email', array(
            'username' => 'testuser@test.com'
        ), array(), $headers);
        // Check if the respense contents are json
        $this->assertJson($client);
        $this->assertRegExp('/{"error":"password.already_requested"}/', $client->getResponse()->getContent(), "Password request not worked");

        $crawler = $client->request('GET', '/api/resetting/reset/'.$resetting_url);
        $this->assertGreaterThan(
            0,
            $crawler->filter('#fos_user_resetting_form')->count()
        );

        // Check for incorrect user
        $crawler = $client->request('POST', '/api/resetting/send-email', array(
            'username' => 'nonvaliduser@test.com'
        ), array(), $headers);
        // Check if the respense contents are json
        $this->assertJson($client);
    }
}
