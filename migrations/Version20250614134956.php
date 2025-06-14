<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614134956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE activity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, rfc_id INTEGER NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_AC74095A51C9CB4B FOREIGN KEY (rfc_id) REFERENCES rfc (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AC74095A51C9CB4B ON activity (rfc_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rfc (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_4F2899EFF47645AE ON rfc (url)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE activity
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rfc
        SQL);
    }
}
