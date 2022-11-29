<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221124192848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE bicing_station_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE recharge_station_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bicing_station (id INT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, status BOOLEAN NOT NULL, adress VARCHAR(255) NOT NULL, capacity INT NOT NULL, mechanical INT NOT NULL, electrical INT NOT NULL, available_slots INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE recharge_station (id INT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, status BOOLEAN NOT NULL, adress VARCHAR(255) NOT NULL, speed_type VARCHAR(255) DEFAULT NULL, connection_type VARCHAR(255) DEFAULT NULL, power DOUBLE PRECISION DEFAULT NULL, current_type VARCHAR(255) DEFAULT NULL, slots INT DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE bicing_station_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE recharge_station_id_seq CASCADE');
        $this->addSql('DROP TABLE bicing_station');
        $this->addSql('DROP TABLE recharge_station');
    }
}
