<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model;

final class AuthenticationDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $paths = $openApi->getPaths();

        // Obtener todas las rutas y reconstruir sin la de login de LexikJWT
        $pathsArray = $paths->getPaths();
        
        // Eliminar el endpoint de login que agrega LexikJWT
        unset($pathsArray['/api/auth/login']);
        
        // Reconstruir paths sin el login de Lexik
        $newPaths = new Model\Paths();
        foreach ($pathsArray as $path => $pathItem) {
            $newPaths->addPath($path, $pathItem);
        }
        
        // Crear nuevo OpenApi con los paths limpios
        $openApi = $openApi->withPaths($newPaths);
        $paths = $openApi->getPaths();

        // Login
        $loginPath = new Model\PathItem(
            post: new Model\Operation(
                operationId: 'postAuthLogin',
                tags: ['Authentication'],
                summary: 'Iniciar sesión',
                description: 'Autenticar usuario y obtener token JWT + refresh token',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['username', 'password'],
                                'properties' => [
                                    'username' => ['type' => 'string', 'format' => 'email', 'example' => 'admin@llaves.com'],
                                    'password' => ['type' => 'string', 'format' => 'password', 'example' => 'admin']
                                ]
                            ]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(
                        description: 'Login exitoso',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => ['type' => 'string', 'description' => 'JWT token (válido 1 hora)'],
                                        'refresh_token' => ['type' => 'string', 'description' => 'Refresh token (válido 30 días)'],
                                        'user' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'integer'],
                                                'email' => ['type' => 'string'],
                                                'roles' => ['type' => 'array', 'items' => ['type' => 'string']]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '401' => new Model\Response(description: 'Credenciales inválidas')
                ]
            )
        );
        $paths->addPath('/api/auth/login', $loginPath);

        // Register
        $registerPath = new Model\PathItem(
            post: new Model\Operation(
                operationId: 'postAuthRegister',
                tags: ['Authentication'],
                summary: 'Registrar usuario',
                description: 'Crear una nueva cuenta de usuario con rol ROLE_USER',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['email', 'password'],
                                'properties' => [
                                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'usuario@ejemplo.com'],
                                    'password' => ['type' => 'string', 'format' => 'password', 'example' => 'mipassword123']
                                ]
                            ]
                        ]
                    ])
                ),
                responses: [
                    '201' => new Model\Response(
                        description: 'Usuario registrado exitosamente',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string'],
                                        'user' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'integer'],
                                                'email' => ['type' => 'string'],
                                                'roles' => ['type' => 'array', 'items' => ['type' => 'string']]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '400' => new Model\Response(description: 'Datos inválidos'),
                    '409' => new Model\Response(description: 'Email ya registrado')
                ]
            )
        );
        $paths->addPath('/api/auth/register', $registerPath);

        // Token de refresco
        $refreshPath = new Model\PathItem(
            post: new Model\Operation(
                operationId: 'postAuthRefresh',
                tags: ['Authentication'],
                summary: 'Renovar token JWT',
                description: 'Obtener un nuevo JWT usando un refresh token válido',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['refresh_token'],
                                'properties' => [
                                    'refresh_token' => ['type' => 'string', 'description' => 'Refresh token obtenido en el login']
                                ]
                            ]
                        ]
                    ])
                ),
                responses: [
                    '200' => new Model\Response(
                        description: 'Token renovado exitosamente',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => ['type' => 'string', 'description' => 'Nuevo JWT token'],
                                        'refresh_token' => ['type' => 'string', 'description' => 'Mismo refresh token'],
                                        'user' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'integer'],
                                                'email' => ['type' => 'string'],
                                                'roles' => ['type' => 'array', 'items' => ['type' => 'string']]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ])
                    ),
                    '401' => new Model\Response(description: 'Refresh token inválido o expirado')
                ]
            )
        );
        $paths->addPath('/api/auth/refresh', $refreshPath);

        return $openApi;
    }
}
