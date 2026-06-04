<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Appointment;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;

class AppointmentProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?Appointment
    {
        if (!$data instanceof Appointment) {
            return $data;
        }

        // Si es una nueva cita o edición
        if ($operation instanceof \ApiPlatform\Metadata\Post || $operation instanceof \ApiPlatform\Metadata\Put) {
            // Asignar usuario actual
            $user = $this->security->getUser();
            if ($user) {
                $data->setUser($user);
            }
            
            // Persistir cambios en la base de datos
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

        return $data;
    }
}
