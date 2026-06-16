<?php

namespace App\Tests\Controller;

use App\Repository\RecordRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecordControllerTest extends WebTestCase
{
    private const ROUTE = '/record';

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndex(): void
    {
        // when
        $this->client->request(
            'GET',
            self::ROUTE
        );

        // then
        self::assertResponseIsSuccessful();
    }

    public function testViewNotFound(): void
    {
        // when
        $this->client->request(
            'GET',
            self::ROUTE.'/999999'
        );

        // then
        self::assertResponseStatusCodeSame(404);
    }

}
