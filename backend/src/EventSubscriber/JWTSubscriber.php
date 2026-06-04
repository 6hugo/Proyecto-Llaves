<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Entity\User;

class JWTSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => ['onAuthenticationSuccess', -10], // Prioridad baja para ejecutar después del bundle
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        // Añadir datos del usuario (el refresh token ya lo añade el bundle)
        $data['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        $event->setData($data);
    }
}
