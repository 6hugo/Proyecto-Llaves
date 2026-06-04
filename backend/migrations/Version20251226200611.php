<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251226200611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "appointment" (id SERIAL NOT NULL, customer_name VARCHAR(255) NOT NULL, customer_email VARCHAR(255) NOT NULL, customer_phone VARCHAR(20) NOT NULL, appointment_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, service_address TEXT NOT NULL, vehicle_make VARCHAR(100) NOT NULL, vehicle_model VARCHAR(100) NOT NULL, vehicle_year VARCHAR(50) DEFAULT NULL, service_type VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, price NUMERIC(10, 2) DEFAULT NULL, notes TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "faq" (id SERIAL NOT NULL, question VARCHAR(255) NOT NULL, answer TEXT NOT NULL, position INT NOT NULL, is_active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE "appointment"');
        $this->addSql('DROP TABLE "faq"');
    }
}
