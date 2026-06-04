<?php

namespace App\Repository;

use App\Entity\Faq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Faq>
 */
class FaqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }

    /**
     * Encuentra todas las FAQs activas ordenadas por posición
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('f.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
