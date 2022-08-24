<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220824204458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE command_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE command (id INT NOT NULL, owner_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, total_price DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8ECAEAD47E3C61F9 ON command (owner_id)');
        $this->addSql('COMMENT ON COLUMN command.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN command.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE command_dish (command_id INT NOT NULL, dish_id INT NOT NULL, PRIMARY KEY(command_id, dish_id))');
        $this->addSql('CREATE INDEX IDX_34D7223533E1689A ON command_dish (command_id)');
        $this->addSql('CREATE INDEX IDX_34D72235148EB0CB ON command_dish (dish_id)');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD47E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE command_dish ADD CONSTRAINT FK_34D7223533E1689A FOREIGN KEY (command_id) REFERENCES command (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE command_dish ADD CONSTRAINT FK_34D72235148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE command_id_seq CASCADE');
        $this->addSql('ALTER TABLE command DROP CONSTRAINT FK_8ECAEAD47E3C61F9');
        $this->addSql('ALTER TABLE command_dish DROP CONSTRAINT FK_34D7223533E1689A');
        $this->addSql('ALTER TABLE command_dish DROP CONSTRAINT FK_34D72235148EB0CB');
        $this->addSql('DROP TABLE command');
        $this->addSql('DROP TABLE command_dish');
    }
}
