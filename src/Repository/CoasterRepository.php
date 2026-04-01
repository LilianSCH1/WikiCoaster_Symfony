<?php

namespace App\Repository;

use App\Entity\Coaster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Coaster>
 */
class CoasterRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private Security $security,
    )
    {
        parent::__construct($registry, Coaster::class);
    }

    public function findFiltered(
        int $parkId = 0,
        int $categoryId = 0,
        int $count = 2,
        int $page = 1,
    ): Paginator
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.park', 'p')
            ->leftJoin('c.categories', 'ca')
        ;

        if ($parkId > 0) {
            $qb->andWhere('p.id = :parkId')
                ->setParameter('parkId', $parkId);
        }

        if ($categoryId > 0) {
            $qb->andWhere('ca.id = :catId')
                ->setParameter('catId', $categoryId);
        }

        $begin = ($page - 1) * $count; // Calcul de l'offset
        $qb->setFirstResult($begin) // OFFSET
            ->setMaxResults($count); // LIMIT

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $qb->andWhere('c.published = true OR (c.author = :author AND c.author IS NOT NULL)')
                ->setParameter('author', $this->security->getUser())
            ;
        }

        return new Paginator($qb->getQuery());
    }
}
