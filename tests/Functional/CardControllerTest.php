<?php

namespace App\Tests\Functional;

use App\Entity\Card;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CardControllerTest extends DatabaseWebTestCase
{
    public function testOwnerCanEditCardAndUploadImage(): void
    {
        $owner = $this->createMerchant('owner');
        $collection = $this->createCollection($owner);
        $card = $this->createCard($collection, 'Avant');
        $this->loginAs($owner);

        $image = sys_get_temp_dir().'/card-test.png';
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

        $this->client->request('GET', sprintf('/card/%d/edit', $card->getId()));
        $this->client->submitForm('Modifier', ['card[myCollection]' => (string) $foreignCollection->getId()]);

        $this->assertResponseStatusCodeSame(403);
        $this->em->clear();
        $this->assertNotSame($foreignCollection->getId(), $this->em->find(Card::class, $card->getId())->getMyCollection()->getId());
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
