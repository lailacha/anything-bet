<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202090515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE connection DROP CONSTRAINT fk_29f7736679f37ae5');
        $this->addSql('DROP INDEX idx_29f7736679f37ae5');
        $this->addSql('ALTER TABLE connection ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE connection DROP id_user_id');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F77366A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_29F77366A76ED395 ON connection (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE connection DROP CONSTRAINT FK_29F77366A76ED395');
        $this->addSql('DROP INDEX IDX_29F77366A76ED395');
        $this->addSql('ALTER TABLE connection ADD id_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE connection DROP user_id');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT fk_29f7736679f37ae5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_29f7736679f37ae5 ON connection (id_user_id)');
    }
}
