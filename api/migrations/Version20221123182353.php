<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221123182353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE api_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE location_id_seq CASCADE');
        $this->addSql('ALTER TABLE user_location DROP CONSTRAINT fk_be136dcba76ed395');
        $this->addSql('ALTER TABLE user_location DROP CONSTRAINT fk_be136dcb64d218e');
        $this->addSql('ALTER TABLE user_recharge_station DROP CONSTRAINT fk_a2b81149a76ed395');
        $this->addSql('ALTER TABLE user_recharge_station DROP CONSTRAINT fk_a2b81149a5aa0401');
        $this->addSql('ALTER TABLE api_token DROP CONSTRAINT fk_7ba2f5eba63bc7a');
        $this->addSql('ALTER TABLE user_bicing_station DROP CONSTRAINT fk_53c135e2a76ed395');
        $this->addSql('ALTER TABLE user_bicing_station DROP CONSTRAINT fk_53c135e27a6a4a16');
        $this->addSql('DROP TABLE user_location');
        $this->addSql('DROP TABLE user_recharge_station');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_bicing_station');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE api_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE location_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_location (user_id INT NOT NULL, location_id INT NOT NULL, PRIMARY KEY(user_id, location_id))');
        $this->addSql('CREATE INDEX idx_be136dcb64d218e ON user_location (location_id)');
        $this->addSql('CREATE INDEX idx_be136dcba76ed395 ON user_location (user_id)');
        $this->addSql('CREATE TABLE user_recharge_station (user_id INT NOT NULL, recharge_station_id INT NOT NULL, PRIMARY KEY(user_id, recharge_station_id))');
        $this->addSql('CREATE INDEX idx_a2b81149a5aa0401 ON user_recharge_station (recharge_station_id)');
        $this->addSql('CREATE INDEX idx_a2b81149a76ed395 ON user_recharge_station (user_id)');
        $this->addSql('CREATE TABLE location (id INT NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, titol VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE api_token (id INT NOT NULL, token_owner_id INT NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7ba2f5eba63bc7a ON api_token (token_owner_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_7ba2f5eb5f37a13b ON api_token (token)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, fullname VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
        $this->addSql('CREATE TABLE user_bicing_station (user_id INT NOT NULL, bicing_station_id INT NOT NULL, PRIMARY KEY(user_id, bicing_station_id))');
        $this->addSql('CREATE INDEX idx_53c135e27a6a4a16 ON user_bicing_station (bicing_station_id)');
        $this->addSql('CREATE INDEX idx_53c135e2a76ed395 ON user_bicing_station (user_id)');
        $this->addSql('ALTER TABLE user_location ADD CONSTRAINT fk_be136dcba76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_location ADD CONSTRAINT fk_be136dcb64d218e FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_recharge_station ADD CONSTRAINT fk_a2b81149a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_recharge_station ADD CONSTRAINT fk_a2b81149a5aa0401 FOREIGN KEY (recharge_station_id) REFERENCES recharge_station (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT fk_7ba2f5eba63bc7a FOREIGN KEY (token_owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_bicing_station ADD CONSTRAINT fk_53c135e2a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_bicing_station ADD CONSTRAINT fk_53c135e27a6a4a16 FOREIGN KEY (bicing_station_id) REFERENCES bicing_station (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
