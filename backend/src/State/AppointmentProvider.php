<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\SecurityBundle\Security;

class AppointmentProvider implements ProviderInterface
{
    public function __construct(
        private AppointmentRepository $appointmentRepository,
        private Security $security
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            return [];
        }

        // Si es una colección (lista de citas)
        if ($operation instanceof \ApiPlatform\Metadata\GetCollection) {
            // Si es admin, devolver todas las citas
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return $this->appointmentRepository->findAll();
            }
            
            // Si es usuario normal, solo sus citas
            return $this->appointmentRepository->findBy(['user' => $user], ['appointmentDate' => 'ASC']);
        }

        // Si es una cita individual
        if (isset($uriVariables['id'])) {
            return $this->appointmentRepository->find($uriVariables['id']);
        }

        return null;
    }
}
