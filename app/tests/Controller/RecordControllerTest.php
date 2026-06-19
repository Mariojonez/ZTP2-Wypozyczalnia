<?php

/**
 * Record controller test.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class RecordControllerTest.
 */
class RecordControllerTest extends WebTestCase
{
    /**
     * Base route.
     */
    private const ROUTE = '/record';

    /**
     * HTTP client.
     */
    private KernelBrowser $client;

    /**
     * Setup test client.
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test index page.
     */
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

    /**
     * Test record not found page.
     */
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
