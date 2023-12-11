<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231125201601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD doctor_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C87F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id)');
        $this->addSql('CREATE INDEX IDX_9474526C87F4FB17 ON comment (doctor_id)');
        $this->addSql('ALTER TABLE doctor ADD user_id INT NOT NULL, ADD service_id INT NOT NULL');
        $this->addSql('ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE doctor ADD CONSTRAINT FK_1FC0F36AED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1FC0F36AA76ED395 ON doctor (user_id)');
        $this->addSql('CREATE INDEX IDX_1FC0F36AED5CA9E6 ON doctor (service_id)');
        $this->addSql('ALTER TABLE medication ADD prescription_id INT NOT NULL');
        $this->addSql('ALTER TABLE medication ADD CONSTRAINT FK_5AEE5B7093DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id)');
        $this->addSql('CREATE INDEX IDX_5AEE5B7093DB413D ON medication (prescription_id)');
        $this->addSql('ALTER TABLE patient ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1ADAD7EBA76ED395 ON patient (user_id)');
        $this->addSql('ALTER TABLE prescription ADD doctor_id INT NOT NULL, ADD patient_id INT NOT NULL');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D987F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctor (id)');
        $this->addSql('ALTER TABLE prescription ADD CONSTRAINT FK_1FBFB8D96B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('CREATE INDEX IDX_1FBFB8D987F4FB17 ON prescription (doctor_id)');
        $this->addSql('CREATE INDEX IDX_1FBFB8D96B899279 ON prescription (patient_id)');
        $this->addSql('ALTER TABLE staff ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_426EF392A76ED395 ON staff (user_id)');
        $this->addSql('ALTER TABLE stay ADD patient_id INT NOT NULL, ADD service_id INT NOT NULL');
        $this->addSql('ALTER TABLE stay ADD CONSTRAINT FK_5E09839C6B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE stay ADD CONSTRAINT FK_5E09839CED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_5E09839C6B899279 ON stay (patient_id)');
        $this->addSql('CREATE INDEX IDX_5E09839CED5CA9E6 ON stay (service_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C87F4FB17');
        $this->addSql('DROP INDEX IDX_9474526C87F4FB17 ON comment');
        $this->addSql('ALTER TABLE comment DROP doctor_id');
        $this->addSql('ALTER TABLE stay DROP FOREIGN KEY FK_5E09839C6B899279');
        $this->addSql('ALTER TABLE stay DROP FOREIGN KEY FK_5E09839CED5CA9E6');
        $this->addSql('DROP INDEX IDX_5E09839C6B899279 ON stay');
        $this->addSql('DROP INDEX IDX_5E09839CED5CA9E6 ON stay');
        $this->addSql('ALTER TABLE stay DROP patient_id, DROP service_id');
        $this->addSql('ALTER TABLE staff DROP FOREIGN KEY FK_426EF392A76ED395');
        $this->addSql('DROP INDEX UNIQ_426EF392A76ED395 ON staff');
        $this->addSql('ALTER TABLE staff DROP user_id');
        $this->addSql('ALTER TABLE doctor DROP FOREIGN KEY FK_1FC0F36AA76ED395');
        $this->addSql('ALTER TABLE doctor DROP FOREIGN KEY FK_1FC0F36AED5CA9E6');
        $this->addSql('DROP INDEX UNIQ_1FC0F36AA76ED395 ON doctor');
        $this->addSql('DROP INDEX IDX_1FC0F36AED5CA9E6 ON doctor');
        $this->addSql('ALTER TABLE doctor DROP user_id, DROP service_id');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D987F4FB17');
        $this->addSql('ALTER TABLE prescription DROP FOREIGN KEY FK_1FBFB8D96B899279');
        $this->addSql('DROP INDEX IDX_1FBFB8D987F4FB17 ON prescription');
        $this->addSql('DROP INDEX IDX_1FBFB8D96B899279 ON prescription');
        $this->addSql('ALTER TABLE prescription DROP doctor_id, DROP patient_id');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBA76ED395');
        $this->addSql('DROP INDEX UNIQ_1ADAD7EBA76ED395 ON patient');
        $this->addSql('ALTER TABLE patient DROP user_id');
        $this->addSql('ALTER TABLE medication DROP FOREIGN KEY FK_5AEE5B7093DB413D');
        $this->addSql('DROP INDEX IDX_5AEE5B7093DB413D ON medication');
        $this->addSql('ALTER TABLE medication DROP prescription_id');
    }
}
