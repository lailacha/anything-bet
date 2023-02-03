<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202092812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE betting_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE group_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE participate_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE participate (id INT NOT NULL, event_id INT DEFAULT NULL, the_user_id INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D02B13871F7E88B ON participate (event_id)');
        $this->addSql('CREATE INDEX IDX_D02B13847BCFD73 ON participate (the_user_id)');
        $this->addSql('ALTER TABLE participate ADD CONSTRAINT FK_D02B13871F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participate ADD CONSTRAINT FK_D02B13847BCFD73 FOREIGN KEY (the_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role DROP CONSTRAINT fk_57698a6a79f37ae5');
        $this->addSql('DROP TABLE role');
        $this->addSql('ALTER TABLE bet DROP amount');
        $this->addSql('ALTER TABLE betting ADD is_won BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE betting RENAME COLUMN score TO amount');
        $this->addSql('ALTER TABLE connection ADD date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE connection ADD ip VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE event ADD the_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE event ADD result VARCHAR(255) NOT NULL');
        $this->addSql('COMMENT ON COLUMN event.start_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA747BCFD73 FOREIGN KEY (the_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3BAE0AA747BCFD73 ON event (the_user_id)');
        $this->addSql('ALTER TABLE friend DROP CONSTRAINT fk_55eeac61ed86ff16');
        $this->addSql('DROP INDEX idx_55eeac61ed86ff16');
        $this->addSql('ALTER TABLE friend DROP id_user_two_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE betting_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE group_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE participate_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, id_user_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_57698a6a79f37ae5 ON role (id_user_id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT fk_57698a6a79f37ae5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participate DROP CONSTRAINT FK_D02B13871F7E88B');
        $this->addSql('ALTER TABLE participate DROP CONSTRAINT FK_D02B13847BCFD73');
        $this->addSql('DROP TABLE participate');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA747BCFD73');
        $this->addSql('DROP INDEX IDX_3BAE0AA747BCFD73');
        $this->addSql('ALTER TABLE event DROP the_user_id');
        $this->addSql('ALTER TABLE event DROP start_at');
        $this->addSql('ALTER TABLE event DROP result');
        $this->addSql('ALTER TABLE bet ADD amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE betting DROP is_won');
        $this->addSql('ALTER TABLE betting RENAME COLUMN amount TO score');
        $this->addSql('ALTER TABLE connection DROP date');
        $this->addSql('ALTER TABLE connection DROP ip');
        $this->addSql('ALTER TABLE friend ADD id_user_two_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT fk_55eeac61ed86ff16 FOREIGN KEY (id_user_two_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_55eeac61ed86ff16 ON friend (id_user_two_id)');
    }
}
