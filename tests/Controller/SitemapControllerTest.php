<?php

namespace App\Tests\Controller;

use App\Entity\Card;
use App\Entity\Deck;
use App\Repository\DeckRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SitemapControllerTest extends WebTestCase
{
    public function testSitemapListsPublicPagesAndPublishedDecks(): void
    {
        $client = static::createClient();

        $card = $this->withId(new Card(), 7);
        $deck = $this->withId(new Deck(), 3);
        $deck->addCard($card);

        $repository = $this->createMock(DeckRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with(['published' => true])
            ->willReturn([$deck]);
        static::getContainer()->set(DeckRepository::class, $repository);

        $client->request('GET', '/sitemap.xml');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/xml; charset=UTF-8');

        $xml = simplexml_load_string($client->getResponse()->getContent());
        $this->assertNotFalse($xml);
        $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $locs = array_map('strval', $xml->xpath('//s:loc'));

        $this->assertSame([
            'https://lilianastrade.julsql.fr/',
            'https://lilianastrade.julsql.fr/deck/',
            'https://lilianastrade.julsql.fr/deck/3',
            'https://lilianastrade.julsql.fr/deck/3/card/7',
        ], $locs);
    }

    private function withId(object $entity, int $id): object
    {
        $property = new \ReflectionProperty($entity, 'id');
        $property->setValue($entity, $id);

        return $entity;
    }
}
