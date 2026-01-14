<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114073102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD COLUMN titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD COLUMN description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD COLUMN date DATE NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD COLUMN lieu VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD COLUMN image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__evenement AS SELECT id, category_id_id FROM evenement');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('CREATE TABLE evenement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id_id INTEGER DEFAULT NULL, CONSTRAINT FK_B26681E9777D11E FOREIGN KEY (category_id_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO evenement (id, category_id_id) SELECT id, category_id_id FROM __temp__evenement');
        $this->addSql('DROP TABLE __temp__evenement');
        $this->addSql('CREATE INDEX IDX_B26681E9777D11E ON evenement (category_id_id)');
    }
}
