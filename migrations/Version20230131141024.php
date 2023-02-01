<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131141024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE betting_group_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE betting_group_user (betting_group_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(betting_group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_A36978E018924EB2 ON betting_group_user (betting_group_id)');
        $this->addSql('CREATE INDEX IDX_A36978E0A76ED395 ON betting_group_user (user_id)');
        $this->addSql('CREATE TABLE betting_group_members (betting_group_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(betting_group_id, user_id))');
        $this->addSql('CREATE INDEX IDX_FEE53E5B18924EB2 ON betting_group_members (betting_group_id)');
        $this->addSql('CREATE INDEX IDX_FEE53E5BA76ED395 ON betting_group_members (user_id)');
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
        $this->addSql('ALTER TABLE betting_group_user ADD CONSTRAINT FK_A36978E018924EB2 FOREIGN KEY (betting_group_id) REFERENCES betting_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_user ADD CONSTRAINT FK_A36978E0A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_members ADD CONSTRAINT FK_FEE53E5B18924EB2 FOREIGN KEY (betting_group_id) REFERENCES betting_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_members ADD CONSTRAINT FK_FEE53E5BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_administrators DROP CONSTRAINT fk_8f384349a76ed395');
        $this->addSql('ALTER TABLE betting_group_administrators DROP CONSTRAINT fk_8f38434918924eb2');
        $this->addSql('DROP TABLE betting_group_administrators');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE betting_group_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('CREATE TABLE betting_group_administrators (user_id INT NOT NULL, betting_group_id INT NOT NULL, PRIMARY KEY(user_id, betting_group_id))');
        $this->addSql('CREATE INDEX idx_8f38434918924eb2 ON betting_group_administrators (betting_group_id)');
        $this->addSql('CREATE INDEX idx_8f384349a76ed395 ON betting_group_administrators (user_id)');
        $this->addSql('ALTER TABLE betting_group_administrators ADD CONSTRAINT fk_8f384349a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_administrators ADD CONSTRAINT fk_8f38434918924eb2 FOREIGN KEY (betting_group_id) REFERENCES betting_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting_group_user DROP CONSTRAINT FK_A36978E018924EB2');
        $this->addSql('ALTER TABLE betting_group_user DROP CONSTRAINT FK_A36978E0A76ED395');
        $this->addSql('ALTER TABLE betting_group_members DROP CONSTRAINT FK_FEE53E5B18924EB2');
        $this->addSql('ALTER TABLE betting_group_members DROP CONSTRAINT FK_FEE53E5BA76ED395');
        $this->addSql('DROP TABLE betting_group_user');
        $this->addSql('DROP TABLE betting_group_members');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
