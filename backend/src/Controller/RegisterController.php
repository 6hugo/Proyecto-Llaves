<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/api/auth/register',
    summary: 'Registrar nuevo usuario',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'usuario@ejemplo.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'mipassword123')
            ]
        )
    ),
    tags: ['Authentication'],
    responses: [
        new OA\Response(
            response: 201,
            description: 'Usuario registrado exitosamente',
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Usuario registrado exitosamente'),
                    new OA\Property(
                        property: 'user',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'email', type: 'string', example: 'usuario@ejemplo.com'),
                            new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string'), example: ['ROLE_USER'])
                        ]
                    )
                ]
            )
        ),
        new OA\Response(
            response: 400,
            description: 'Datos inválidos',
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'error', type: 'string', example: 'Email y contraseña son requeridos')
                ]
            )
        ),
        new OA\Response(
            response: 409,
            description: 'Email ya registrado',
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'error', type: 'string', example: 'Este email ya está registrado')
                ]
            )
        )
    ]
)]
class RegisterController extends AbstractController
{
    #[Route('/api/auth/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json([
                'error' => 'Email y contraseña son requeridos'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Verificar si el email ya existe
        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json([
                'error' => 'Este email ya está registrado'
            ], Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);
        
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
                'error' => (string) $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ]
        ], Response::HTTP_CREATED);
    }
}
