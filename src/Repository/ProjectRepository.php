<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    //https://symfonycasts.com/screencast/doctrine-relations/pagination
    /**
     * @return Project[] Returns an array of Project objects
     */
    public function Paginator()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getLastProjects()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.creationDate', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function getNotDelProjects()
    {
        try {
            return $this->createQueryBuilder('p')
                ->select('COUNT(p)')
                ->where('p.deliveryDate IS NULL')
                ->getQuery()
                ->getSingleScalarResult();
        } 
        catch (NoResultException $e) {} 
        catch (NonUniqueResultException $e) {}
    }

    public function getDelProjects()
    {
        try {
            return $this->createQueryBuilder('p')
                ->select('COUNT(p)')
                ->where('p.deliveryDate IS NOT NULL')
                ->getQuery()
                ->getSingleScalarResult();
        } 
        catch (NoResultException $e) {}
        catch (NonUniqueResultException $e) {}
    }
}
