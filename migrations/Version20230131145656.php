<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230131145656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bet ADD id_event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bet ADD amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9B212C041E FOREIGN KEY (id_event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FBF0EC9B212C041E ON bet (id_event_id)');
        $this->addSql('ALTER TABLE betting ADD id_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE betting ADD id_bet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE betting ADD score INT NOT NULL');
        $this->addSql('ALTER TABLE betting ADD CONSTRAINT FK_5EDD2FE279F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betting ADD CONSTRAINT FK_5EDD2FE2FE4669CB FOREIGN KEY (id_bet_id) REFERENCES bet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5EDD2FE279F37AE5 ON betting (id_user_id)');
        $this->addSql('CREATE INDEX IDX_5EDD2FE2FE4669CB ON betting (id_bet_id)');
        $this->addSql('ALTER TABLE connection ADD id_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE connection ADD CONSTRAINT FK_29F7736679F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_29F7736679F37AE5 ON connection (id_user_id)');
        $this->addSql('ALTER TABLE friend ADD id_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE friend ADD id_user_two_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC6179F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC61ED86FF16 FOREIGN KEY (id_user_two_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_55EEAC6179F37AE5 ON friend (id_user_id)');
        $this->addSql('CREATE INDEX IDX_55EEAC61ED86FF16 ON friend (id_user_two_id)');
        $this->addSql('ALTER TABLE points ADD id_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE points ADD id_betting_group_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE points ADD CONSTRAINT FK_27BA8E2979F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE points ADD CONSTRAINT FK_27BA8E29FB181B63 FOREIGN KEY (id_betting_group_id) REFERENCES betting_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_27BA8E2979F37AE5 ON points (id_user_id)');
        $this->addSql('CREATE INDEX IDX_27BA8E29FB181B63 ON points (id_betting_group_id)');
        $this->addSql('ALTER TABLE role ADD id_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A79F37AE5 FOREIGN KEY (id_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_57698A6A79F37AE5 ON role (id_user_id)');
        $this->addSql('ALTER TABLE "user" DROP roles');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE role DROP CONSTRAINT FK_57698A6A79F37AE5');
        $this->addSql('DROP INDEX IDX_57698A6A79F37AE5');
        $this->addSql('ALTER TABLE role DROP id_user_id');
        $this->addSql('ALTER TABLE "user" ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE friend DROP CONSTRAINT FK_55EEAC6179F37AE5');
        $this->addSql('ALTER TABLE friend DROP CONSTRAINT FK_55EEAC61ED86FF16');
        $this->addSql('DROP INDEX IDX_55EEAC6179F37AE5');
        $this->addSql('DROP INDEX IDX_55EEAC61ED86FF16');
        $this->addSql('ALTER TABLE friend DROP id_user_id');
        $this->addSql('ALTER TABLE friend DROP id_user_two_id');
        $this->addSql('ALTER TABLE betting DROP CONSTRAINT FK_5EDD2FE279F37AE5');
        $this->addSql('ALTER TABLE betting DROP CONSTRAINT FK_5EDD2FE2FE4669CB');
        $this->addSql('DROP INDEX IDX_5EDD2FE279F37AE5');
        $this->addSql('DROP INDEX IDX_5EDD2FE2FE4669CB');
        $this->addSql('ALTER TABLE betting DROP id_user_id');
        $this->addSql('ALTER TABLE betting DROP id_bet_id');
        $this->addSql('ALTER TABLE betting DROP score');
        $this->addSql('ALTER TABLE connection DROP CONSTRAINT FK_29F7736679F37AE5');
        $this->addSql('DROP INDEX IDX_29F7736679F37AE5');
        $this->addSql('ALTER TABLE connection DROP id_user_id');
        $this->addSql('ALTER TABLE points DROP CONSTRAINT FK_27BA8E2979F37AE5');
        $this->addSql('ALTER TABLE points DROP CONSTRAINT FK_27BA8E29FB181B63');
        $this->addSql('DROP INDEX IDX_27BA8E2979F37AE5');
        $this->addSql('DROP INDEX IDX_27BA8E29FB181B63');
        $this->addSql('ALTER TABLE points DROP id_user_id');
        $this->addSql('ALTER TABLE points DROP id_betting_group_id');
        $this->addSql('ALTER TABLE bet DROP CONSTRAINT FK_FBF0EC9B212C041E');
        $this->addSql('DROP INDEX IDX_FBF0EC9B212C041E');
        $this->addSql('ALTER TABLE bet DROP id_event_id');
        $this->addSql('ALTER TABLE bet DROP amount');
    }
}
