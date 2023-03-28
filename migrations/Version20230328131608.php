<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230328131608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer CHANGE siret siret INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E096F337C32 FOREIGN KEY (tariff_zone_id) REFERENCES tariff_zone (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E099393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('CREATE INDEX IDX_81398E096F337C32 ON customer (tariff_zone_id)');
        $this->addSql('CREATE INDEX IDX_81398E099393F8FE ON customer (partner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E096F337C32');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E099393F8FE');
        $this->addSql('DROP INDEX IDX_81398E096F337C32 ON customer');
        $this->addSql('DROP INDEX IDX_81398E099393F8FE ON customer');
        $this->addSql('ALTER TABLE customer CHANGE siret siret BIGINT UNSIGNED DEFAULT NULL');
    }
}
