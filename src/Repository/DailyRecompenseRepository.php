<?php

namespace App\Repository;

use App\Entity\DailyRecompense;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Connection>
 *
 * @method DailyRecompense|null find($id, $lockMode = null, $lockVersion = null)
 * @method DailyRecompense|null findOneBy(array $criteria, array $orderBy = null)
 * @method DailyRecompense[]    findAll()
 * @method DailyRecompense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DailyRecompenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyRecompense::class);
    }

    public function save(DailyRecompense $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DailyRecompense $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Connection[] Returns an array of Connection objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Connection
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getGroupCanReceiveRecompenses(User $user) : array
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        //same but with query builder
        $qb = $conn->createQueryBuilder();
        $qb->select('DISTINCT *')
            ->from('betting_group')
            ->innerJoin('betting_group', 'betting_group_members', 'bgm', 'betting_group.id = bgm.betting_group_id')
            ->where("NOT EXISTS (SELECT * FROM daily_recompense dr
                WHERE bgm.user_id = :user_id AND dr.date >= DATE_TRUNC('day', now()) AND dr.date < now()
                     AND dr.betting_group_id = betting_group.id
                     AND dr.id IS NOT NULL)")
           ->andWhere('bgm.user_id = :user_id');

        $sql = $qb->getSQL();
        $stmt = $conn->prepare($sql);
        return $stmt->executeQuery(['user_id' => $user->getId()])->fetchAllAssociative();
    }

    public function receiveRecompense(?\Symfony\Component\Security\Core\User\UserInterface $getUser, $id)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $qb = $conn->createQueryBuilder();
        $qb->insert('daily_recompense')
            ->values(
                [
                    'date' => 'NOW()',
                    'betting_group_id' => ':betting_group_id',
                    'user_id' => ':user_id',
                ]
            );

        $sql = $qb->getSQL();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['betting_group_id' => $id, 'user_id' => $getUser->getId()]);
    }
}