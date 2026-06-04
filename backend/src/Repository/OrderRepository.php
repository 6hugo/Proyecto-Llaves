<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Buscar pedidos por usuario
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Buscar pedidos por estado
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtener estadísticas de pedidos
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->count([]),
            'pending' => $this->count(['status' => Order::STATUS_PENDING]),
            'confirmed' => $this->count(['status' => Order::STATUS_CONFIRMED]),
            'shipped' => $this->count(['status' => Order::STATUS_SHIPPED]),
            'completed' => $this->count(['status' => Order::STATUS_COMPLETED]),
            'cancelled' => $this->count(['status' => Order::STATUS_CANCELLED]),
        ];
    }
}
