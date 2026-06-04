<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crea un usuario administrador por defecto',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Verificar si ya existe un admin
        $existingAdmin = $this->userRepository->findOneBy(['email' => 'admin@llaves.com']);
        
        if ($existingAdmin) {
            $io->warning('El usuario administrador ya existe.');
            return Command::SUCCESS;
        }

        // Crear usuario administrador
        $admin = new User();
        $admin->setEmail('admin@llaves.com');
        $admin->setRoles(['ROLE_ADMIN']);
        
        // Hashear contraseña
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'test123'
        );
        $admin->setPassword($hashedPassword);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('Usuario administrador creado exitosamente!');
        $io->table(
            ['Campo', 'Valor'],
            [
                ['Email', 'admin@llaves.com'],
                ['Contraseña', 'test123'],
                ['Rol', 'ROLE_ADMIN'],
            ]
        );
        $io->warning('⚠️  Cambia esta contraseña después del primer login!');

        return Command::SUCCESS;
    }
}
