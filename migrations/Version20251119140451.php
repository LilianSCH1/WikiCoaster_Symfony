<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119140451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coaster ADD park_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coaster ADD CONSTRAINT FK_F6312A7844990C25 FOREIGN KEY (park_id) REFERENCES park (id)');
        $this->addSql('CREATE INDEX IDX_F6312A7844990C25 ON coaster (park_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coaster DROP FOREIGN KEY FK_F6312A7844990C25');
        $this->addSql('DROP INDEX IDX_F6312A7844990C25 ON coaster');
        $this->addSql('ALTER TABLE coaster DROP park_id');
    }
}
