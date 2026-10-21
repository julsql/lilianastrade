<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomeExposesSeoMetadata(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSame('fr', $crawler->filter('html')->attr('lang'));
        $this->assertSelectorTextContains('title', "Liliana's Trade");
        $this->assertNotEmpty($crawler->filter('meta[name="description"]')->attr('content'));
        $this->assertSame(
            'https://lilianastrade.julsql.fr/',
            $crawler->filter('link[rel="canonical"]')->attr('href')
        );
        $this->assertCount(0, $crawler->filter('meta[name="robots"][content*="noindex"]'));
    }
}
