<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024131246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_7ba2f5eba63bc7a');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7BA2F5EBA63BC7A ON api_token (token_owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_7BA2F5EBA63BC7A');
        $this->addSql('CREATE INDEX idx_7ba2f5eba63bc7a ON api_token (token_owner_id)');
    }
}
