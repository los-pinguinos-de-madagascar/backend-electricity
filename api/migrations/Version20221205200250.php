<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221205200250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, sender_id INT NOT NULL, reciever_id INT NOT NULL, data TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307FF624B39D ON message (sender_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F5D5C928D ON message (reciever_id)');
        $this->addSql('CREATE TABLE user_bicing_station (user_id INT NOT NULL, bicing_station_id INT NOT NULL, PRIMARY KEY(user_id, bicing_station_id))');
        $this->addSql('CREATE INDEX IDX_53C135E2A76ED395 ON user_bicing_station (user_id)');
        $this->addSql('CREATE INDEX IDX_53C135E27A6A4A16 ON user_bicing_station (bicing_station_id)');
        $this->addSql('CREATE TABLE user_recharge_station (user_id INT NOT NULL, recharge_station_id INT NOT NULL, PRIMARY KEY(user_id, recharge_station_id))');
        $this->addSql('CREATE INDEX IDX_A2B81149A76ED395 ON user_recharge_station (user_id)');
        $this->addSql('CREATE INDEX IDX_A2B81149A5AA0401 ON user_recharge_station (recharge_station_id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5D5C928D FOREIGN KEY (reciever_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_bicing_station ADD CONSTRAINT FK_53C135E2A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_bicing_station ADD CONSTRAINT FK_53C135E27A6A4A16 FOREIGN KEY (bicing_station_id) REFERENCES bicing_station (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_recharge_station ADD CONSTRAINT FK_A2B81149A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_recharge_station ADD CONSTRAINT FK_A2B81149A5AA0401 FOREIGN KEY (recharge_station_id) REFERENCES recharge_station (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F5D5C928D');
        $this->addSql('ALTER TABLE user_bicing_station DROP CONSTRAINT FK_53C135E2A76ED395');
        $this->addSql('ALTER TABLE user_bicing_station DROP CONSTRAINT FK_53C135E27A6A4A16');
        $this->addSql('ALTER TABLE user_recharge_station DROP CONSTRAINT FK_A2B81149A76ED395');
        $this->addSql('ALTER TABLE user_recharge_station DROP CONSTRAINT FK_A2B81149A5AA0401');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE user_bicing_station');
        $this->addSql('DROP TABLE user_recharge_station');
    }
}
