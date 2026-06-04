<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OrderProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?Order
    {
        if (!$data instanceof Order) {
            return $data;
        }

        // Si es una nueva orden
        if ($operation instanceof \ApiPlatform\Metadata\Post) {
            // Asignar usuario actual
            $user = $this->security->getUser();
            $data->setUser($user);

            // Procesar items y verificar stock
            foreach ($data->getItems() as $item) {
                $product = $item->getProduct();

                // Verificar stock disponible
                if ($product->getStock() < $item->getQuantity()) {
                    throw new BadRequestHttpException(
                        sprintf(
                            'Stock insuficiente para %s %s. Disponible: %d, Solicitado: %d',
                            $product->getBrand(),
                            $product->getModel(),
                            $product->getStock(),
                            $item->getQuantity()
                        )
                    );
                }

                // Guardar el precio actual del producto
                $item->setPrice($product->getPrice());

                // Restar del stock
                $product->setStock($product->getStock() - $item->getQuantity());
                $this->entityManager->persist($product);
            }

            // Calcular total
            $data->calculateTotal();
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
