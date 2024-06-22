<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginWithBadCredentials(): void
    {
        try {
            $crawler = $this->client->request('GET', '/login');
            $form = $crawler->selectButton('login')->form();
            $form['email'] = 'wrong_email';
            $form['password'] = 'wrong_password';
            $this->client->submit($form);
            $this->assertTrue($this->client->getResponse()->isRedirect());
            $crawler = $this->client->followRedirect();
            $this->assertContains('Identifiants invalides.', [$crawler->filter('div.alert-danger')->text()]);
        } catch (\Exception $e) {
            $this->fail('An exception occurred: ' . $e->getMessage());
        }
    }

    public function testLogin(): void
    {
        $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    
    public function testLoginWithGoodCredentials(): void
    {
        try {
            $crawler = $this->client->request('GET', '/login');
            $form = $crawler->selectButton('login')->form();
            $form['email'] = 'test12@test.com';
            $form['password'] = 'test4200';
            $this->client->submit($form);
            $this->assertTrue($this->client->getResponse()->isRedirect());
            $crawler = $this->client->followRedirect();
            $this->assertContains('/event/', [$this->client->getRequest()->getRequestUri()]);
        } catch (\Exception $e) {
            $this->fail('An exception occurred: ' . $e->getMessage());
        }
    }

    public function testLogout(): void
    {
        $this->client->request('GET', '/logout');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }
}