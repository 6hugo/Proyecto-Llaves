<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    /**
     * Encuentra citas por estado
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->setParameter('status', $status)
            ->orderBy('a.appointmentDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra citas futuras
     */
    public function findUpcoming(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.appointmentDate > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('a.appointmentDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
