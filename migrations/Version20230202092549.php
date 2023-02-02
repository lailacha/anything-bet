<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202092549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE connection_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE daily_recompense_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE daily_recompense (id INT NOT NULL, user_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E3783DBDA76ED395 ON daily_recompense (user_id)');
        $this->addSql('COMMENT ON COLUMN daily_recompense.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE daily_recompense ADD CONSTRAINT FK_E3783DBDA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE connection DROP CONSTRAINT fk_29f77366a76ed395');
        $this->addSql('DROP TABLE connection');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE daily_recompense_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE connection_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE connection (id INT NOT NULL, user_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_29f77366a76ed395 ON connection (user_id)');
        $this->addSql('COMMENT ON COLUMN connection.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT fk_29f77366a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE daily_recompense DROP CONSTRAINT FK_E3783DBDA76ED395');
        $this->addSql('DROP TABLE daily_recompense');
    }
}
