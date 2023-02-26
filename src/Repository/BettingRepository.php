<?php

namespace App\Repository;

use App\Entity\Bet;
use App\Entity\Betting;
use App\Entity\Event;
use App\Entity\Points;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Betting>
 *
 * @method Betting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Betting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Betting[]    findAll()
 * @method Betting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Betting::class);
    }

    public function save(Betting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Betting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Betting[] Returns an array of Betting objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Betting
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findUsersByEvent(int $eventId, int $betId): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('u.id, p.score')
            ->innerJoin(Bet::class, 'bet', 'WITH', 'bet.id = b.idBet')
            ->innerJoin(Event::class, 'e', 'WITH', 'e.id = bet.event')
            ->innerJoin(User::class, 'u', 'WITH', 'u.id = b.idUser')
            ->innerJoin(Points::class, 'p', 'WITH', 'p.idUser = u.id')
            ->where('e.id = :event')
            ->andWhere('bet.id = :bet')
            ->setParameter('event', $eventId)
            ->setParameter('bet', $betId)
        ;



        return $qb->getQuery()->getResult();
    }
}
