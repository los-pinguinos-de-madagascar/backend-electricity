<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221021174229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE bicing_station_id_seq CASCADE');
        $this->addSql('ALTER TABLE bicing_station ADD latitude DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE bicing_station ADD longitude DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE bicing_station ADD status BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE bicing_station ADD adress VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE bicing_station_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE bicing_station DROP latitude');
        $this->addSql('ALTER TABLE bicing_station DROP longitude');
        $this->addSql('ALTER TABLE bicing_station DROP status');
        $this->addSql('ALTER TABLE bicing_station DROP adress');
    }
}
