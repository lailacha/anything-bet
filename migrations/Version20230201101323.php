<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230201101323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_request ADD betting_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE group_request ADD CONSTRAINT FK_BD97DB9318924EB2 FOREIGN KEY (betting_group_id) REFERENCES betting_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BD97DB9318924EB2 ON group_request (betting_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE group_request DROP CONSTRAINT FK_BD97DB9318924EB2');
        $this->addSql('DROP INDEX IDX_BD97DB9318924EB2');
        $this->addSql('ALTER TABLE group_request DROP betting_group_id');
    }
}
