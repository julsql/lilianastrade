<?php

namespace App\Repository;

use App\Entity\MyCollection;
use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MyCollection>
 *
 * @method MyCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method MyCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method MyCollection[]    findAll()
 * @method MyCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MyCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MyCollection::class);
    }

    public function save(MyCollection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MyCollection $entity, bool $flush = false): void
    {
        $cardRepository = $this->getEntityManager()->getRepository(Card::class);

        // clean the [objets] properly
        $cards = $entity->getCard();
        foreach($cards as $card) {
            $cardRepository->remove($card, $flush);
        }
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MyCollection[] Returns an array of MyCollection objects
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

//    images function findOneBySomeField($value): ?MyCollection
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
