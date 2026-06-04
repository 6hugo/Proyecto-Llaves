<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-sample-products',
    description: 'Crea productos de ejemplo para pruebas',
)]
class CreateSampleProductsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $products = [
            ['Audi', 'A3', 2020, '89.99', 5, 'Llave con mando a distancia para Audi A3'],
            ['Audi', 'A4', 2021, '95.50', 3, 'Llave inteligente para Audi A4'],
            ['BMW', '320i', 2019, '110.00', 8, 'Llave con mando para BMW Serie 3'],
            ['BMW', 'X5', 2022, '125.00', 4, 'Llave inteligente para BMW X5'],
            ['Mercedes', 'Clase C', 2020, '105.00', 6, 'Llave con mando para Mercedes Clase C'],
            ['Mercedes', 'GLA', 2021, '115.00', 7, 'Llave inteligente para Mercedes GLA'],
            ['Volkswagen', 'Golf', 2018, '75.00', 10, 'Llave con mando para VW Golf'],
            ['Volkswagen', 'Tiguan', 2020, '85.00', 9, 'Llave con mando para VW Tiguan'],
            ['Seat', 'León', 2019, '70.00', 12, 'Llave con mando para Seat León'],
            ['Seat', 'Ateca', 2021, '80.00', 8, 'Llave inteligente para Seat Ateca'],
            ['Ford', 'Focus', 2019, '65.00', 15, 'Llave con mando para Ford Focus'],
            ['Ford', 'Kuga', 2020, '72.50', 11, 'Llave con mando para Ford Kuga'],
            ['Renault', 'Clio', 2020, '60.00', 14, 'Llave con tarjeta para Renault Clio'],
            ['Renault', 'Megane', 2021, '68.00', 10, 'Llave con tarjeta para Renault Megane'],
            ['Peugeot', '308', 2019, '62.00', 13, 'Llave con mando para Peugeot 308'],
        ];

        foreach ($products as [$brand, $model, $year, $price, $stock, $description]) {
            $product = new Product();
            $product->setBrand($brand);
            $product->setModel($model);
            $product->setYear($year);
            $product->setPrice($price);
            $product->setStock($stock);
            $product->setDescription($description);
            
            $imageName = 'key.jpg'; // Por defecto
            if (stripos($brand, 'Audi') !== false) {
                $imageName = 'audi.jpg';
            } elseif (stripos($brand, 'BMW') !== false) {
                $imageName = 'bmw.jpg';
            } elseif (stripos($brand, 'Mercedes') !== false) {
                $imageName = 'mercedes.jpg';
            }
            
            $product->setImageUrl('/assets/images/' . $imageName);

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        $io->success('Se crearon ' . count($products) . ' productos de ejemplo exitosamente!');

        return Command::SUCCESS;
    }
}
