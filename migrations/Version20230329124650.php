<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230329124650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer CHANGE postal_code postal_code VARCHAR(10) NOT NULL, CHANGE partner_id partner_id INT NOT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E099393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('CREATE INDEX IDX_81398E099393F8FE ON customer (partner_id)');
        $this->addSql('ALTER TABLE customer RENAME INDEX fk_81398e096f337c32 TO IDX_81398E096F337C32');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E099393F8FE');
        $this->addSql('DROP INDEX IDX_81398E099393F8FE ON customer');
        $this->addSql('ALTER TABLE customer CHANGE partner_id partner_id INT DEFAULT NULL, CHANGE postal_code postal_code VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE customer RENAME INDEX idx_81398e096f337c32 TO FK_81398E096F337C32');
    }
}
