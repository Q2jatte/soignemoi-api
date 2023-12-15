<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231213163456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stay ADD doctor_id INT NOT NULL');
        $this->addSql('ALTER TABLE stay ADD CONSTRAINT FK_5E09839C87F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id)');
        $this->addSql('CREATE INDEX IDX_5E09839C87F4FB17 ON stay (doctor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stay DROP FOREIGN KEY FK_5E09839C87F4FB17');
        $this->addSql('DROP INDEX IDX_5E09839C87F4FB17 ON stay');
        $this->addSql('ALTER TABLE stay DROP doctor_id');
    }
}
