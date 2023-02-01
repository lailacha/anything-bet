<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230201124824 extends AbstractMigration
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
        $this->addSql('CREATE SEQUENCE group_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE points_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE role_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bet (id INT NOT NULL, id_event_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, amount INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FBF0EC9B212C041E ON bet (id_event_id)');
        $this->addSql('CREATE TABLE betting (id INT NOT NULL, id_user_id INT DEFAULT NULL, id_bet_id INT DEFAULT NULL, score INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5EDD2FE279F37AE5 ON betting (id_user_id)');
        $this->addSql('CREATE INDEX IDX_5EDD2FE2FE4669CB ON betting (id_bet_id)');
        $this->addSql('CREATE TABLE betting_group (id INT NOT NULL, name VARCHAR(255) NOT NULL, user_max INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, code VARCHAR(23) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4DF9C8FB77153098 ON betting_group (code)');
        $this->addSql('COMMENT ON COLUMN betting_group.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE betting_group_administrators (betting_group_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(betting_group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_8F38434918924EB2 ON betting_group_administrators (betting_group_id)');
        $this->addSql('CREATE INDEX IDX_8F384349A76ED395 ON betting_group_administrators (user_id)');
        $this->addSql('CREATE TABLE betting_group_members (betting_group_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(betting_group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_FEE53E5B18924EB2 ON betting_group_members (betting_group_id)');
        $this->addSql('CREATE INDEX IDX_FEE53E5BA76ED395 ON betting_group_members (user_id)');
        $this->addSql('CREATE TABLE connection (id INT NOT NULL, id_user_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29F7736679F37AE5 ON connection (id_user_id)');
        $this->addSql('CREATE TABLE event (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, finish_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN event.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN event.finish_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE friend (id INT NOT NULL, id_user_id INT DEFAULT NULL, id_user_two_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_55EEAC6179F37AE5 ON friend (id_user_id)');
        $this->addSql('CREATE INDEX IDX_55EEAC61ED86FF16 ON friend (id_user_two_id)');
        $this->addSql('CREATE TABLE group_request (id INT NOT NULL, user_id INT DEFAULT NULL, betting_group_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, is_approved BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BD97DB93A76ED395 ON group_request (user_id)');
        $this->addSql('CREATE INDEX IDX_BD97DB9318924EB2 ON group_request (betting_group_id)');
        $this->addSql('COMMENT ON COLUMN group_request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE points (id INT NOT NULL, id_user_id INT DEFAULT NULL, id_betting_group_id INT DEFAULT NULL, score INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_27BA8E2979F37AE5 ON points (id_user_id)');
        $this->addSql('CREATE INDEX IDX_27BA8E29FB181B63 ON points (id_betting_group_id)');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, id_user_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_57698A6A79F37AE5 ON role (id_user_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, pseudo VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, avatar VARCHAR(128) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9B212C041E FOREIGN KEY (id_event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting ADD CONSTRAINT FK_5EDD2FE279F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting ADD CONSTRAINT FK_5EDD2FE2FE4669CB FOREIGN KEY (id_bet_id) REFERENCES bet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_administrators ADD CONSTRAINT FK_8F38434918924EB2 FOREIGN KEY (betting_group_id) REFERENCES betting_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_administrators ADD CONSTRAINT FK_8F384349A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_members ADD CONSTRAINT FK_FEE53E5B18924EB2 FOREIGN KEY (betting_group_id) REFERENCES betting_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_members ADD CONSTRAINT FK_FEE53E5BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F7736679F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC6179F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC61ED86FF16 FOREIGN KEY (id_user_two_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_request ADD CONSTRAINT FK_BD97DB93A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE group_request ADD CONSTRAINT FK_BD97DB9318924EB2 FOREIGN KEY (betting_group_id) REFERENCES betting_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE points ADD CONSTRAINT FK_27BA8E2979F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE points ADD CONSTRAINT FK_27BA8E29FB181B63 FOREIGN KEY (id_betting_group_id) REFERENCES betting_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
        $this->addSql('DROP SEQUENCE group_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE points_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE role_id_seq CASCADE');
        $this->addSql('ALTER TABLE bet DROP CONSTRAINT FK_FBF0EC9B212C041E');
        $this->addSql('ALTER TABLE betting DROP CONSTRAINT FK_5EDD2FE279F37AE5');
        $this->addSql('ALTER TABLE betting DROP CONSTRAINT FK_5EDD2FE2FE4669CB');
        $this->addSql('ALTER TABLE betting_group_administrators DROP CONSTRAINT FK_8F38434918924EB2');
        $this->addSql('ALTER TABLE betting_group_administrators DROP CONSTRAINT FK_8F384349A76ED395');
        $this->addSql('ALTER TABLE betting_group_members DROP CONSTRAINT FK_FEE53E5B18924EB2');
        $this->addSql('ALTER TABLE betting_group_members DROP CONSTRAINT FK_FEE53E5BA76ED395');
        $this->addSql('ALTER TABLE connection DROP CONSTRAINT FK_29F7736679F37AE5');
        $this->addSql('ALTER TABLE friend DROP CONSTRAINT FK_55EEAC6179F37AE5');
        $this->addSql('ALTER TABLE friend DROP CONSTRAINT FK_55EEAC61ED86FF16');
        $this->addSql('ALTER TABLE group_request DROP CONSTRAINT FK_BD97DB93A76ED395');
        $this->addSql('ALTER TABLE group_request DROP CONSTRAINT FK_BD97DB9318924EB2');
        $this->addSql('ALTER TABLE points DROP CONSTRAINT FK_27BA8E2979F37AE5');
        $this->addSql('ALTER TABLE points DROP CONSTRAINT FK_27BA8E29FB181B63');
        $this->addSql('ALTER TABLE role DROP CONSTRAINT FK_57698A6A79F37AE5');
        $this->addSql('DROP TABLE bet');
        $this->addSql('DROP TABLE betting');
        $this->addSql('DROP TABLE betting_group');
        $this->addSql('DROP TABLE betting_group_administrators');
        $this->addSql('DROP TABLE betting_group_members');
        $this->addSql('DROP TABLE connection');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE friend');
        $this->addSql('DROP TABLE group_request');
        $this->addSql('DROP TABLE points');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
