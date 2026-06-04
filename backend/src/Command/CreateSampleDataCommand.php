<?php

namespace App\Command;

use App\Entity\Faq;
use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-sample-data',
    description: 'Creates sample FAQs and Appointments for development',
    hidden: false,
)]
class CreateSampleDataCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Creating Sample FAQs');
        $this->createSampleFaqs($io);

        $io->section('Creating Sample Appointments');
        $this->createSampleAppointments($io);

        $io->success('Sample data created successfully!');
        return Command::SUCCESS;
    }

    private function createSampleFaqs(SymfonyStyle $io): void
    {
        $faqs = [
            [
                'question' => '¿Cuáles son sus horarios de atención?',
                'answer' => 'Estamos abiertos de lunes a viernes de 8:00 AM a 6:00 PM, y sábados de 9:00 AM a 2:00 PM. Los domingos estamos cerrados.',
                'position' => 1,
            ],
            [
                'question' => '¿Ofrecen servicio a domicilio?',
                'answer' => 'Sí, ofrecemos servicio a domicilio para duplicados de llaves y servicios de emergencia. Contáctenos para más información.',
                'position' => 2,
            ],
            [
                'question' => '¿Cuánto tiempo tarda un duplicado de llave?',
                'answer' => 'La mayoría de duplicados se completan en 15-30 minutos. Para llaves especiales puede tomar más tiempo.',
                'position' => 3,
            ],
            [
                'question' => '¿Hacen programación de llaves de vehículos?',
                'answer' => 'Sí, contamos con equipo especializado para programación de llaves y controles remotos de la mayoría de marcas de vehículos.',
                'position' => 4,
            ],
            [
                'question' => '¿Qué métodos de pago aceptan?',
                'answer' => 'Aceptamos efectivo, tarjetas de crédito/débito, transferencias bancarias y billeteras digitales.',
                'position' => 5,
            ],
            [
                'question' => '¿Ofrecen garantía en sus trabajos?',
                'answer' => 'Sí, todos nuestros trabajos tienen garantía de 6 meses. Si hay algún defecto, lo corregimos sin costo.',
                'position' => 6,
            ],
        ];

        foreach ($faqs as $faqData) {
            $faq = new Faq();
            $faq->setQuestion($faqData['question']);
            $faq->setAnswer($faqData['answer']);
            $faq->setPosition($faqData['position']);
            $faq->setIsActive(true);
            $faq->setCreatedAt(new \DateTime());
            $faq->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($faq);
            $io->writeln('✓ Created FAQ: ' . $faqData['question']);
        }

        $this->entityManager->flush();
    }

    private function createSampleAppointments(SymfonyStyle $io): void
    {
        $appointments = [
            [
                'customerName' => 'Juan Pérez',
                'customerEmail' => 'juan@example.com',
                'customerPhone' => '+34612345678',
                'appointmentDate' => '2025-12-28 10:00:00',
                'serviceAddress' => 'Calle Principal 123, Madrid',
                'vehicleMake' => 'Toyota',
                'vehicleModel' => 'Corolla',
                'vehicleYear' => '2020',
                'serviceType' => 'key_duplication',
                'status' => 'pending',
                'notes' => 'Necesito duplicar las llaves del vehículo',
            ],
            [
                'customerName' => 'María García',
                'customerEmail' => 'maria@example.com',
                'customerPhone' => '+34623456789',
                'appointmentDate' => '2025-12-28 14:00:00',
                'serviceAddress' => 'Avenida del Mar 456, Barcelona',
                'vehicleMake' => 'BMW',
                'vehicleModel' => '3 Series',
                'vehicleYear' => '2022',
                'serviceType' => 'key_programming',
                'status' => 'confirmed',
                'notes' => 'Programación de llave inteligente',
            ],
            [
                'customerName' => 'Carlos López',
                'customerEmail' => 'carlos@example.com',
                'customerPhone' => '+34634567890',
                'appointmentDate' => '2025-12-29 09:00:00',
                'serviceAddress' => 'Plaza Mayor 789, Valencia',
                'vehicleMake' => 'Volkswagen',
                'vehicleModel' => 'Golf',
                'vehicleYear' => '2019',
                'serviceType' => 'remote_programming',
                'status' => 'pending',
                'notes' => null,
            ],
            [
                'customerName' => 'Ana Rodríguez',
                'customerEmail' => 'ana@example.com',
                'customerPhone' => '+34645678901',
                'appointmentDate' => '2025-12-29 15:30:00',
                'serviceAddress' => 'Paseo de la Castellana 321, Madrid',
                'vehicleMake' => 'Audi',
                'vehicleModel' => 'A4',
                'vehicleYear' => '2021',
                'serviceType' => 'transponder_key',
                'status' => 'pending',
                'notes' => 'Llave con transponder dañada',
            ],
            [
                'customerName' => 'Pedro Martínez',
                'customerEmail' => 'pedro@example.com',
                'customerPhone' => '+34656789012',
                'appointmentDate' => '2025-12-30 11:00:00',
                'serviceAddress' => 'Calle Balmes 654, Barcelona',
                'vehicleMake' => 'Mercedes-Benz',
                'vehicleModel' => 'C-Class',
                'vehicleYear' => '2023',
                'serviceType' => 'key_extraction',
                'status' => 'completed',
                'notes' => 'Extracción de llave rota',
            ],
        ];

        foreach ($appointments as $appointmentData) {
            $appointment = new Appointment();
            $appointment->setCustomerName($appointmentData['customerName']);
            $appointment->setCustomerEmail($appointmentData['customerEmail']);
            $appointment->setCustomerPhone($appointmentData['customerPhone']);
            $appointment->setAppointmentDate(new \DateTime($appointmentData['appointmentDate']));
            $appointment->setServiceAddress($appointmentData['serviceAddress']);
            $appointment->setVehicleMake($appointmentData['vehicleMake']);
            $appointment->setVehicleModel($appointmentData['vehicleModel']);
            $appointment->setVehicleYear($appointmentData['vehicleYear']);
            $appointment->setServiceType($appointmentData['serviceType']);
            $appointment->setStatus($appointmentData['status']);
            $appointment->setNotes($appointmentData['notes']);
            $appointment->setCreatedAt(new \DateTime());
            $appointment->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($appointment);
            $io->writeln('✓ Created Appointment: ' . $appointmentData['customerName'] . ' - ' . $appointmentData['serviceType']);
        }

        $this->entityManager->flush();
    }
}
