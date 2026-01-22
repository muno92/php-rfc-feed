<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260122131005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__rfc AS SELECT id, url, version FROM rfc');
        $this->addSql('DROP TABLE rfc');
        $this->addSql('CREATE TABLE rfc (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL, version VARCHAR(50) NOT NULL)');
        $this->addSql('INSERT INTO rfc (id, url, version) SELECT id, url, version FROM __temp__rfc');
        $this->addSql('DROP TABLE __temp__rfc');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F2899EFF47645AE ON rfc (url)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__rfc AS SELECT id, url, version FROM rfc');
        $this->addSql('DROP TABLE rfc');
        $this->addSql('CREATE TABLE rfc (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) NOT NULL, version VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO rfc (id, url, version) SELECT id, url, version FROM __temp__rfc');
        $this->addSql('DROP TABLE __temp__rfc');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4F2899EFF47645AE ON rfc (url)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_TITLE_VERSION ON rfc (title, version)');
    }
}
