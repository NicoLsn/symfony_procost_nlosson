<?php

namespace App\Repository;

use App\Entity\UserProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method UserProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProject[]    findAll()
 * @method UserProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProject::class);
    }

    /**
     * @return UserProject[] Returns an array of UserProject objects
     */

    public function getByUser($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getByProject($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.project = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function getTotalTime()
    {
        try {
            return $this->createQueryBuilder('t')
                ->select('SUM(t.time) as totalTime')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }

    }

    public function getLastTime()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.addedAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }


    public function getTopEmployee($id)
    {
        try {
            return $this->createQueryBuilder('p')
                ->setParameter('val', $id)
                ->andWhere('p.user = :val')
                ->leftJoin('p.user', 'u')
                ->select('SUM(p.time*u.cost) as cost')
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }
    }

    public function getDetailsProject(int $id)
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT p.user)')
            ->andWhere('p.project = :val')
            ->setParameter('val', $id)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalCost(int $id, int $idProject)
    {
        try {
            return $this->createQueryBuilder('p')
                ->where('p.project = :project')
                ->andWhere('p.user = :val')
                ->setParameter('val', $id)
                ->setParameter('project', $idProject)
                ->leftJoin('p.user', 'u')
                ->select('SUM(p.time*u.cost) as totalCost')
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }

    }
}
