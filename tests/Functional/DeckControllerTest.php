<?php

namespace App\Tests\Functional;

class DeckControllerTest extends DatabaseWebTestCase
{
    public function testCardShowResolvesBothEntitiesFromRoute(): void
    {
        $owner = $this->createMerchant('owner');
        $card = $this->createCard($this->createCollection($owner), 'Llanowar Elves');
        $deck = $this->createDeck($owner, true, $card);

        $this->client->request('GET', sprintf('/deck/%d/card/%d', $deck->getId(), $card->getId()));

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Llanowar Elves');
    }

    public function testCardShowReturns404WhenCardIsNotInDeck(): void
    {
        $owner = $this->createMerchant('owner');
        $card = $this->createCard($this->createCollection($owner));
        $deck = $this->createDeck($owner, true);

        $this->client->request('GET', sprintf('/deck/%d/card/%d', $deck->getId(), $card->getId()));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testPrivateDeckIsHiddenFromOtherMerchants(): void
    {
        $deck = $this->createDeck($this->createMerchant('owner'), false);
        $this->loginAs($this->createMerchant('other'));

        $this->client->request('GET', '/deck/'.$deck->getId());

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditRequiresLogin(): void
    {
        $deck = $this->createDeck($this->createMerchant('owner'), true);

        $this->client->request('GET', sprintf('/deck/%d/edit', $deck->getId()));

        $this->assertResponseRedirects('/login');
    }

    public function testOtherMerchantCannotEditOrDeleteDeck(): void
    {
        $deck = $this->createDeck($this->createMerchant('owner'), true);
        $this->loginAs($this->createMerchant('other'));

        $this->client->request('GET', sprintf('/deck/%d/edit', $deck->getId()));
        $this->assertResponseStatusCodeSame(403);

        $this->client->request('POST', '/deck/'.$deck->getId());
        $this->assertResponseStatusCodeSame(403);
    }

    public function testOtherMerchantCannotCreateDeckForSomeoneElse(): void
    {
        $owner = $this->createMerchant('owner');
        $this->loginAs($this->createMerchant('other'));

        $this->client->request('GET', '/deck/new/'.$owner->getId());

        $this->assertResponseStatusCodeSame(403);
    }

    public function testOwnerCanEditDeck(): void
    {
        $owner = $this->createMerchant('owner');
        $deck = $this->createDeck($owner, false);
        $this->loginAs($owner);

        $this->client->request('GET', sprintf('/deck/%d/edit', $deck->getId()));
        $this->client->submitForm('Modifier', ['deck[nom]' => 'Deck renommé']);

        $this->assertResponseRedirects('/deck/');
        $this->em->clear();
        $this->assertSame('Deck renommé', $this->em->find($deck::class, $deck->getId())->getName());
    }
}
