<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Buscar productos con stock disponible
     */
    public function findAvailable(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.stock > 0')
            ->orderBy('p.brand', 'ASC')
            ->addOrderBy('p.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Buscar por marca
     */
    public function findByBrand(string $brand): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.brand LIKE :brand')
            ->setParameter('brand', '%' . $brand . '%')
            ->orderBy('p.model', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
