<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260122130159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate RFC title to Activity title';
    }

    public function up(Schema $schema): void
    {
        // Copy title from rfc table to activity table for existing records
        $this->addSql('UPDATE activity SET title = (SELECT title FROM rfc WHERE rfc.id = activity.rfc_id)');
    }

    public function down(Schema $schema): void
    {
        // Set all activity titles to NULL
        $this->addSql('UPDATE activity SET title = NULL');
    }
}
