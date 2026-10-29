<?php

namespace App\Tests\Functional;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Merchant;
use App\Entity\MyCollection;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels sur une vraie base PostgreSQL (DATABASE_URL, suffixe _test),
 * schéma recréé avant chaque test.
 */
abstract class DatabaseWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    protected function createMerchant(string $pseudo, array $roles = []): Merchant
    {
        $merchant = (new Merchant())->setPseudo($pseudo);
        $user = (new User())
            ->setEmail($pseudo.'@example.com')
            ->setPassword('unused')
            ->setRoles($roles)
            ->setMerchant($merchant);
        $merchant->setUser($user);
        $this->em->persist($merchant);
        $this->em->persist($user);
        $this->em->flush();

        return $merchant;
    }

    protected function createCollection(Merchant $owner, string $name = 'Collection'): MyCollection
    {
        $collection = (new MyCollection())->setName($name)->setOwner($owner);
        $this->em->persist($collection);
        $this->em->flush();

        return $collection;
    }

    protected function createCard(MyCollection $collection, string $name = 'Carte'): Card
    {
        $card = (new Card())->setName($name)->setMyCollection($collection);
        $this->em->persist($card);
        $this->em->flush();

        return $card;
    }

    protected function createDeck(Merchant $owner, bool $published, Card ...$cards): Deck
    {
        $deck = (new Deck())->setName('Deck')->setDescription('Description')->setPublished($published)->setOwner($owner);
        foreach ($cards as $card) {
            $deck->addCard($card);
        }
        $this->em->persist($deck);
        $this->em->flush();

        return $deck;
    }

    protected function loginAs(Merchant $merchant): void
    {
        $this->client->loginUser($merchant->getUser());
    }
}
