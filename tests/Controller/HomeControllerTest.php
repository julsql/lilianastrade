<?php

namespace App\Tests\Controller;

use App\Repository\DeckRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class HomeControllerTest extends WebTestCase
{
    private const BASE_URL = 'https://lilianastrade.julsql.fr';

    public function testHomeExposesSeoMetadata(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertCommonSeoTags($crawler, self::BASE_URL.'/');

        $jsonLd = json_decode($crawler->filter('script[type="application/ld+json"]')->text(), true);
        $this->assertIsArray($jsonLd);
        $this->assertSame('https://schema.org', $jsonLd['@context']);
        $types = array_column($jsonLd['@graph'], '@type');
        $this->assertContains('WebSite', $types);
        $this->assertContains('WebApplication', $types);
    }

    public function testDeckIndexHasItsOwnTitleAndDescription(): void
    {
        $client = static::createClient();
        $repository = $this->createMock(DeckRepository::class);
        $repository->method('findBy')->willReturn([]);
        static::getContainer()->set(DeckRepository::class, $repository);

        $crawler = $client->request('GET', '/deck/');

        $this->assertResponseIsSuccessful();
        $this->assertCommonSeoTags($crawler, self::BASE_URL.'/deck/');
        $this->assertStringStartsWith('Decks Magic', $crawler->filter('title')->text());
    }

    private function assertCommonSeoTags(Crawler $crawler, string $expectedUrl): void
    {
        $this->assertSame('fr', $crawler->filter('html')->attr('lang'));

        $title = $crawler->filter('title')->text();
        $this->assertGreaterThanOrEqual(50, mb_strlen($title), $title);
        $this->assertLessThanOrEqual(60, mb_strlen($title), $title);

        $description = $this->meta($crawler, 'name', 'description');
        $this->assertGreaterThanOrEqual(140, mb_strlen($description), $description);
        $this->assertLessThanOrEqual(160, mb_strlen($description), $description);

        $this->assertSame($expectedUrl, $crawler->filter('link[rel="canonical"]')->attr('href'));
        $this->assertSame($title, $this->meta($crawler, 'property', 'og:title'));
        $this->assertSame($description, $this->meta($crawler, 'property', 'og:description'));
        $this->assertSame($expectedUrl, $this->meta($crawler, 'property', 'og:url'));
        $this->assertSame('website', $this->meta($crawler, 'property', 'og:type'));
        $this->assertSame('fr_FR', $this->meta($crawler, 'property', 'og:locale'));
        $this->assertStringStartsWith(self::BASE_URL.'/images/', $this->meta($crawler, 'property', 'og:image'));
        $this->assertSame('summary_large_image', $this->meta($crawler, 'name', 'twitter:card'));
        $this->assertNotEmpty($this->meta($crawler, 'name', 'theme-color'));
        $this->assertCount(1, $crawler->filter('link[rel="apple-touch-icon"]'));
        $this->assertCount(1, $crawler->filter('h1'));
        $this->assertCount(0, $crawler->filter('img:not([alt])'));
        $this->assertCount(0, $crawler->filter('meta[name="robots"][content*="noindex"]'));
    }

    private function meta(Crawler $crawler, string $attribute, string $name): string
    {
        $node = $crawler->filter(sprintf('meta[%s="%s"]', $attribute, $name));
        $this->assertCount(1, $node, $name);

        return (string) $node->attr('content');
    }
}
