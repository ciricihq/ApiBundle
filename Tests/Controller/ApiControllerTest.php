<?php

namespace Cirici\ApiBundle\Tests\Controller;

class ApiControllerTest extends BaseApiTestCase
{
    public function testTypicalOauthLogin()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        $token_values = $this->getAccessToken($client, $this->api_client);

        // We should get access token
        $this->assertRegExp('/access_token/', json_encode($token_values), "Access token is not generated propertly");

        // Check for authorization_code from a user/password login
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/oauth/v2/auth?client_id=' . $this->public_id .'&response_type=code&redirect_uri=users');
        // this should redirect to login area
        $this->assertGreaterThan(0, $crawler->filter('form#login')->count(), "The login form cannot be reached");

        // We'll try to fill fields with previous generated testuser values
        $loginform = $crawler->selectButton('_submit')->form();
        $loginform['_username'] = "testuser";
        $loginform['_password'] = "test";
        $client->followRedirects(true);
        $crawler = $client->submit($loginform);
        $this->assertGreaterThan(0, $crawler->filter('form[name=fos_oauth_server_authorize_form]')->count(), "The authorize form cannot be reached");

        // Authorize app
        $client->followRedirects(false);
        $authorizeform = $crawler->selectButton('accepted')->form();
        $crawler = $client->submit($authorizeform);
        // We should get 302 status code because we are redirecting to the requested redirect_uri
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "The status code received is not 302");

        // Testing check credentials endpoint
        $client->followRedirects(true);
        $parameters = array(
            '_username' => "testuser",
            '_password' => "test"
        );
        $crawler = $client->request('POST', '/oauth/v2/auth_login_check', $parameters);
        $this->assertNotEquals(500, $client->getResponse()->getStatusCode());
        // We expect 404 code because correct login redirects a non-existing
        // url. If the login would be bad it would redirect to 200 code page
        // with a form to provide correct user and password
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testRefreshToken()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        $token_values = $this->getAccessToken($client, $this->api_client);

        // get the new values
        $url = "/oauth/v2/token";
        $crawler = $client->request('POST', $url, array(
            'client_id' => $this->api_client->getPublicId(),
            'client_secret' => $this->api_client->getSecret(),
            'refresh_token' => $token_values['refresh_token'],
            'grant_type' => 'refresh_token',
        ));
        $this->assertRegExp('/access_token/', $client->getResponse()->getContent(), "Access token from RefreshToken is not generated propertly");
    }

    public function testPasswordGrantLogin()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create testing client
        $api_client = $this->createTestingApiClient();

        // Get the token
        $url = "/oauth/v2/token";
        // We can do this with post or get but in real app we'll do with post
        // or header, so let's try it!!
        $crawler = $client->request('POST', $url, array(
            'client_id' => $api_client->getPublicId(),
            'client_secret' => $api_client->getSecret(),
            'grant_type' => 'password',
            'username' => 'testuser',
            'password' => 'test',
        ));
        // We should get access token
        $this->assertRegExp('/access_token/', $client->getResponse()->getContent(), "Access token is not generated propertly");
        $this->assertRegExp('/refresh_token/', $client->getResponse()->getContent(), "Access token is not generated propertly");
    }
}
