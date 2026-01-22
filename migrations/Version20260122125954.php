<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260122125954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__activity AS SELECT id, rfc_id, status, created_at FROM activity');
        $this->addSql('DROP TABLE activity');
        $this->addSql('CREATE TABLE activity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, rfc_id INTEGER NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, title VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_AC74095A51C9CB4B FOREIGN KEY (rfc_id) REFERENCES rfc (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO activity (id, rfc_id, status, created_at) SELECT id, rfc_id, status, created_at FROM __temp__activity');
        $this->addSql('DROP TABLE __temp__activity');
        $this->addSql('CREATE INDEX IDX_AC74095A51C9CB4B ON activity (rfc_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__activity AS SELECT id, status, created_at, rfc_id FROM activity');
        $this->addSql('DROP TABLE activity');
        $this->addSql('CREATE TABLE activity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , rfc_id INTEGER NOT NULL, CONSTRAINT FK_AC74095A51C9CB4B FOREIGN KEY (rfc_id) REFERENCES rfc (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO activity (id, status, created_at, rfc_id) SELECT id, status, created_at, rfc_id FROM __temp__activity');
        $this->addSql('DROP TABLE __temp__activity');
        $this->addSql('CREATE INDEX IDX_AC74095A51C9CB4B ON activity (rfc_id)');
    }
}
