<?php

namespace App\Repository;

use App\Entity\BettingGroup;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Points;

/**
 * @extends ServiceEntityRepository<BettingGroup>
 *
 * @method BettingGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method BettingGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method BettingGroup[]    findAll()
 * @method BettingGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BettingGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BettingGroup::class);
    }

    public function save(BettingGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BettingGroup $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getGroupsByUserWithScore(User $user)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select('p.score, b.name, b.id, b.cover')
            ->innerJoin(Points::class, 'p', 'WITH', 'p.idBettingGroup = b.id')
            ->where('p.idUser = :user')
            ->andWhere('p.idBettingGroup = b.id')
            ->setParameter('user', $user->getId());

        return $qb->getQuery()->getResult();
    }


//    /**
//     * @return BettingGroup[] Returns an array of BettingGroup objects
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

//    public function findOneBySomeField($value): ?BettingGroup
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function search(mixed $data)
    {
        $query = $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC');

        if(isset($data['name'])) {
            $query->andWhere('b.name LIKE :name')
                ->setParameter('name', '%'.$data['name'].'%');
        }

        return $query->getQuery()->getResult();
    }

}
