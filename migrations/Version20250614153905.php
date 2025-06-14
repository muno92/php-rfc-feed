<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614153905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__rfc AS SELECT id, title, url FROM rfc
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rfc
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rfc (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, version VARCHAR(50) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO rfc (id, title, url) SELECT id, title, url FROM __temp__rfc
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__rfc
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_4F2899EFF47645AE ON rfc (url)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_TITLE_VERSION ON rfc (title, version)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__rfc AS SELECT id, title, url FROM rfc
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rfc
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rfc (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO rfc (id, title, url) SELECT id, title, url FROM __temp__rfc
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__rfc
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_4F2899EFF47645AE ON rfc (url)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_4F2899EF2B36786B ON rfc (title)
        SQL);
    }
}
