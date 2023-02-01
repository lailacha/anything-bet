<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131141628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE betting_group_administrators (betting_group_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(betting_group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_8F38434918924EB2 ON betting_group_administrators (betting_group_id)');
        $this->addSql('CREATE INDEX IDX_8F384349A76ED395 ON betting_group_administrators (user_id)');
        $this->addSql('ALTER TABLE betting_group_administrators ADD CONSTRAINT FK_8F38434918924EB2 FOREIGN KEY (betting_group_id) REFERENCES betting_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_administrators ADD CONSTRAINT FK_8F384349A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_user DROP CONSTRAINT fk_a36978e018924eb2');
        $this->addSql('ALTER TABLE betting_group_user DROP CONSTRAINT fk_a36978e0a76ed395');
        $this->addSql('DROP TABLE betting_group_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE betting_group_user (betting_group_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(betting_group_id, user_id))');
        $this->addSql('CREATE INDEX idx_a36978e0a76ed395 ON betting_group_user (user_id)');
        $this->addSql('CREATE INDEX idx_a36978e018924eb2 ON betting_group_user (betting_group_id)');
        $this->addSql('ALTER TABLE betting_group_user ADD CONSTRAINT fk_a36978e018924eb2 FOREIGN KEY (betting_group_id) REFERENCES betting_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_user ADD CONSTRAINT fk_a36978e0a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_administrators DROP CONSTRAINT FK_8F38434918924EB2');
        $this->addSql('ALTER TABLE betting_group_administrators DROP CONSTRAINT FK_8F384349A76ED395');
        $this->addSql('DROP TABLE betting_group_administrators');
    }
}
