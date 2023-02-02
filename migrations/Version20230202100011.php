<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202100011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE daily_recompense ADD betting_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE daily_recompense ADD CONSTRAINT FK_E3783DBD18924EB2 FOREIGN KEY (betting_group_id) REFERENCES betting_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E3783DBD18924EB2 ON daily_recompense (betting_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE daily_recompense DROP CONSTRAINT FK_E3783DBD18924EB2');
        $this->addSql('DROP INDEX IDX_E3783DBD18924EB2');
        $this->addSql('ALTER TABLE daily_recompense DROP betting_group_id');
    }
}
