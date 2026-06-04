<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\SecurityBundle\Security;

class OrderProvider implements ProviderInterface
{
    public function __construct(
        private OrderRepository $orderRepository,
        private Security $security
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            return [];
        }

        // Si es una colección (lista de pedidos)
        if ($operation instanceof \ApiPlatform\Metadata\GetCollection) {
            // Si es admin, devolver todos los pedidos
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return $this->orderRepository->findAll();
            }
            
            // Si es usuario normal, solo sus pedidos
            return $this->orderRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
        }

        // Si es un pedido individual
        if (isset($uriVariables['id'])) {
            return $this->orderRepository->find($uriVariables['id']);
        }

        return null;
    }
}
