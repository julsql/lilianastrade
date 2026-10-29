<?php

namespace App\Tests\Functional;

use App\Entity\Card;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CardControllerTest extends DatabaseWebTestCase
{
    private ?string $tmpImage = null;

    protected function tearDown(): void
    {
        $projectDir = static::getContainer()->getParameter('kernel.project_dir');
        foreach (glob($projectDir.'/var/test-uploads/cards/*') ?: [] as $file) {
            unlink($file);
        }
        if ($this->tmpImage && is_file($this->tmpImage)) {
            unlink($this->tmpImage);
        }

        parent::tearDown();
    }

    public function testOwnerCanEditCardAndUploadImage(): void
    {
        $owner = $this->createMerchant('owner');
        $collection = $this->createCollection($owner);
        $card = $this->createCard($collection, 'Avant');
        $this->loginAs($owner);

        $image = $this->tmpImage = sys_get_temp_dir().'/card-test.png';
        file_put_contents($image, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC'));

        $crawler = $this->client->request('GET', sprintf('/card/%d/edit', $card->getId()));
        $form = $crawler->selectButton('Modifier')->form(['card[nom]' => 'Après']);
        $form['card[imageFile][file]']->upload($image);
        $this->client->submit($form);

        $this->assertResponseRedirects('/card/'.$card->getId());
        $this->em->clear();
        $updated = $this->em->find(Card::class, $card->getId());
        $this->assertSame('Après', $updated->getName());
        $this->assertMatchesRegularExpression('/^card-test-[0-9a-f]+\.png$/', $updated->getImageName());
        $this->assertFileExists(static::getContainer()->getParameter('kernel.project_dir').'/var/test-uploads/cards/'.$updated->getImageName());
    }

    public function testOwnerCannotMoveCardIntoAnotherMerchantCollection(): void
    {
        $owner = $this->createMerchant('owner');
        $card = $this->createCard($this->createCollection($owner));
        $foreignCollection = $this->createCollection($this->createMerchant('other'), 'Étrangère');
        $this->loginAs($owner);

        $crawler = $this->client->request('GET', sprintf('/card/%d/edit', $card->getId()));
        $this->assertCount(0, $crawler->filter(sprintf('select[name="card[myCollection]"] option[value="%d"]', $foreignCollection->getId())));

        // valeur hors des choix proposés : le formulaire doit la rejeter
        $values = $crawler->selectButton('Modifier')->form()->getPhpValues();
        $values['card']['myCollection'] = (string) $foreignCollection->getId();
        $this->client->request('POST', sprintf('/card/%d/edit', $card->getId()), $values);

        $this->assertResponseStatusCodeSame(422);
        $this->em->clear();
        $this->assertNotSame($foreignCollection->getId(), $this->em->find(Card::class, $card->getId())->getMyCollection()->getId());
    }

    public function testCannotAttachCardToAnotherMerchantDeck(): void
    {
        $owner = $this->createMerchant('owner');
        $card = $this->createCard($this->createCollection($owner));
        $ownDeck = $this->createDeck($owner, false);
        $foreignDeck = $this->createDeck($this->createMerchant('other'), false);
        $this->loginAs($owner);

        $crawler = $this->client->request('GET', sprintf('/card/%d/edit', $card->getId()));
        $options = $crawler->filter('select[name="card[decks][]"] option')->each(fn ($o) => (int) $o->attr('value'));
        $this->assertSame([$ownDeck->getId()], $options);

        $values = $crawler->selectButton('Modifier')->form()->getPhpValues();
        $values['card']['decks'] = [(string) $foreignDeck->getId()];
        $this->client->request('POST', sprintf('/card/%d/edit', $card->getId()), $values);

        $this->assertResponseStatusCodeSame(422);
        $this->em->clear();
        $this->assertCount(0, $this->em->find($foreignDeck::class, $foreignDeck->getId())->getCards());
    }

    public function testOtherMerchantCannotSeeEditOrDeleteCard(): void
    {
        $card = $this->createCard($this->createCollection($this->createMerchant('owner')));
        $this->loginAs($this->createMerchant('other'));

        foreach ([['GET', '/card/%d'], ['GET', '/card/%d/edit'], ['POST', '/card/%d']] as [$method, $path]) {
            $this->client->request($method, sprintf($path, $card->getId()));
            $this->assertResponseStatusCodeSame(403, $method.' '.$path);
        }
    }
}
