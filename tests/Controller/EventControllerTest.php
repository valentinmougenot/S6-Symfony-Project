<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndex(): void
    {
        $this->logIn();
        $this->client->request('GET', '/event/');
        $this->assertResponseIsSuccessful();
    }

    public function testNewEvent(): void
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/event/new');
        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('form[name=event]')->form([
          'event[title]' => 'Test Event',
          'event[description]' => 'This is a test event',
          'event[date]' => '2024-07-01T10:00',
          'event[max_size]' => 100,
          'event[public]' => 1,
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/event/');
        $this->client->followRedirect();
    }

    public function testEditEvent(): void
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/event/4/edit');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Modifier')->form([
            'event[title]' => 'Updated Event',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/event/');
        $this->client->followRedirect();
    }

    public function testListEvents(): void
    {
        $this->client->request('GET', '/event/list');
        $this->assertResponseIsSuccessful();
    }

    private function logIn()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test12@test.com');
        $this->client->loginUser($testUser);
    }
}
