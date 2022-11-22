<?php

namespace App\Repository;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Merchant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 *
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function save(Card $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Card $entity, bool $flush = false): void
    {
        $deckRepository = $this->getEntityManager()->getRepository(Deck::class);

        // get rid of the ManyToMany relation with Deck
        $decks = $deckRepository->findCardDecks($entity);
        foreach($decks as $deck) {
            $deck->removeCard($entity);
            $this->getEntityManager()->persist($deck);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        // Type
        // get rid of the ManyToMany relation with the Type and Card
        $types = $entity->getType();
        foreach($types as $type) {
            $entity->removeType($type);
            $this->getEntityManager()->persist($type);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        // Color
        // get rid of the ManyToMany relation with the Color and Card
        $colors = $entity->getColor();
        foreach($colors as $color) {
            $entity->removeColor($color);
            $this->getEntityManager()->persist($color);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        // Mana
        // get rid of the ManyToMany relation with the Mana and Card
        $manas = $entity->getMana();
        foreach($manas as $mana) {
            $entity->removeMana($manas);
            $this->getEntityManager()->persist($manas);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        // Edition
        // get rid of the ManyToMany relation with the Edition and Card
        $editions = $entity->getEdition();
        foreach($editions as $edition) {
            $entity->removeEdition($edition);
            $this->getEntityManager()->persist($edition);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        // Rarity
        // get rid of the ManyToMany relation with the Rarity and Card
        $raritys = $entity->getRarity();
        foreach($raritys as $rarity) {
            $entity->removeRarity($rarity);
            $this->getEntityManager()->persist($rarity);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        // Card
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return [Objet][] Returns an array of [Objet] objects for a member
     */
    public function findMerchantCard(Merchant $merchant): array
    {
        return $this->createQueryBuilder('o')
             ->leftJoin('o.myCollection', 'c')
             ->andWhere('c.owner = :merchant')
             ->setParameter('merchant', $merchant)
             ->getQuery()
             ->getResult()
         ;
    }

//    /**
//     * @return Mycards[] Returns an array of Mycards objects
//     */
//    images function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    images function findOneBySomeField($value): ?Mycards
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
