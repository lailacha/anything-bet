<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230212131717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bet DROP CONSTRAINT fk_fbf0ec9b212c041e');
        $this->addSql('DROP INDEX idx_fbf0ec9b212c041e');
        $this->addSql('ALTER TABLE bet RENAME COLUMN id_event_id TO event_id');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9B71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FBF0EC9B71F7E88B ON bet (event_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bet DROP CONSTRAINT FK_FBF0EC9B71F7E88B');
        $this->addSql('DROP INDEX IDX_FBF0EC9B71F7E88B');
        $this->addSql('ALTER TABLE bet RENAME COLUMN event_id TO id_event_id');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT fk_fbf0ec9b212c041e FOREIGN KEY (id_event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_fbf0ec9b212c041e ON bet (id_event_id)');
    }
}
