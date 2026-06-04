<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251227180931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "appointment" ADD COLUMN user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "appointment" ADD CONSTRAINT FK_FE38E3A4A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_FE38E3A4A76ED395 ON "appointment" (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_FE38E3A4A76ED395');
        $this->addSql('ALTER TABLE "appointment" DROP CONSTRAINT FK_FE38E3A4A76ED395');
        $this->addSql('ALTER TABLE "appointment" DROP COLUMN user_id');
    }
}
