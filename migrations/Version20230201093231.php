<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230201093231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE bet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE betting_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE betting_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE connection_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE friend_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE points_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bet (id INT NOT NULL, id_event_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, amount INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FBF0EC9B212C041E ON bet (id_event_id)');
        $this->addSql('CREATE TABLE betting (id INT NOT NULL, id_user_id INT DEFAULT NULL, id_bet_id INT DEFAULT NULL, score INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5EDD2FE279F37AE5 ON betting (id_user_id)');
        $this->addSql('CREATE INDEX IDX_5EDD2FE2FE4669CB ON betting (id_bet_id)');
        $this->addSql('CREATE TABLE betting_group (id INT NOT NULL, name VARCHAR(255) NOT NULL, user_max INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN betting_group.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE connection (id INT NOT NULL, id_user_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29F7736679F37AE5 ON connection (id_user_id)');
        $this->addSql('CREATE TABLE event (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finish_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN event.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.finish_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE friend (id INT NOT NULL, id_user_id INT DEFAULT NULL, id_user_two_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_55EEAC6179F37AE5 ON friend (id_user_id)');
        $this->addSql('CREATE INDEX IDX_55EEAC61ED86FF16 ON friend (id_user_two_id)');
        $this->addSql('CREATE TABLE points (id INT NOT NULL, id_user_id INT DEFAULT NULL, id_betting_group_id INT DEFAULT NULL, score INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_27BA8E2979F37AE5 ON points (id_user_id)');
        $this->addSql('CREATE INDEX IDX_27BA8E29FB181B63 ON points (id_betting_group_id)');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, id_user_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_57698A6A79F37AE5 ON role (id_user_id)');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9B212C041E FOREIGN KEY (id_event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting ADD CONSTRAINT FK_5EDD2FE279F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting ADD CONSTRAINT FK_5EDD2FE2FE4669CB FOREIGN KEY (id_bet_id) REFERENCES bet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F7736679F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC6179F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC61ED86FF16 FOREIGN KEY (id_user_two_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE points ADD CONSTRAINT FK_27BA8E2979F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE points ADD CONSTRAINT FK_27BA8E29FB181B63 FOREIGN KEY (id_betting_group_id) REFERENCES betting_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD is_verified BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD pseudo VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD first_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD last_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD avatar VARCHAR(128) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE bet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE betting_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE betting_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE connection_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE event_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE friend_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE points_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('ALTER TABLE bet DROP CONSTRAINT FK_FBF0EC9B212C041E');
        $this->addSql('ALTER TABLE betting DROP CONSTRAINT FK_5EDD2FE279F37AE5');
        $this->addSql('ALTER TABLE betting DROP CONSTRAINT FK_5EDD2FE2FE4669CB');
        $this->addSql('ALTER TABLE connection DROP CONSTRAINT FK_29F7736679F37AE5');
        $this->addSql('ALTER TABLE friend DROP CONSTRAINT FK_55EEAC6179F37AE5');
        $this->addSql('ALTER TABLE friend DROP CONSTRAINT FK_55EEAC61ED86FF16');
        $this->addSql('ALTER TABLE points DROP CONSTRAINT FK_27BA8E2979F37AE5');
        $this->addSql('ALTER TABLE points DROP CONSTRAINT FK_27BA8E29FB181B63');
        $this->addSql('ALTER TABLE role DROP CONSTRAINT FK_57698A6A79F37AE5');
        $this->addSql('DROP TABLE bet');
        $this->addSql('DROP TABLE betting');
        $this->addSql('DROP TABLE betting_group');
        $this->addSql('DROP TABLE connection');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE friend');
        $this->addSql('DROP TABLE points');
        $this->addSql('DROP TABLE role');
        $this->addSql('ALTER TABLE "user" DROP is_verified');
        $this->addSql('ALTER TABLE "user" DROP pseudo');
        $this->addSql('ALTER TABLE "user" DROP first_name');
        $this->addSql('ALTER TABLE "user" DROP last_name');
        $this->addSql('ALTER TABLE "user" DROP avatar');
    }
}
