<?php

namespace App\Tests\Functional;

class MyCollectionControllerTest extends DatabaseWebTestCase
{
    public function testEditResolvesEntitiesFromRouteAndRedirectsToCollection(): void
    {
        $owner = $this->createMerchant('owner');
        $this->createCollection($owner, 'Décalage d\'id');
        $collection = $this->createCollection($owner, 'Avant');
        $this->loginAs($owner);

        $this->client->request('GET', sprintf('/collection/%d/edit/%d', $owner->getId(), $collection->getId()));
        $this->client->submitForm('Modifier', ['my_collection[name]' => 'Après']);

        $this->assertResponseRedirects('/collection/'.$collection->getId());
    }

    public function testOtherMerchantCannotEditCollection(): void
    {
        $owner = $this->createMerchant('owner');
        $collection = $this->createCollection($owner);
        $this->loginAs($this->createMerchant('other'));

        $this->client->request('GET', sprintf('/collection/%d/edit/%d', $owner->getId(), $collection->getId()));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testNewRedirectsToMerchantPage(): void
    {
        $owner = $this->createMerchant('owner');
        $this->loginAs($owner);

        $this->client->request('GET', '/collection/new/'.$owner->getId());
        $this->client->submitForm('Sauvegarder', ['my_collection[name]' => 'Nouvelle']);

        $this->assertResponseRedirects('/merchant/'.$owner->getId());
    }
}
