<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211101024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coaster ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE coaster ADD CONSTRAINT FK_F6312A78F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F6312A78F675F31B ON coaster (author_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coaster DROP FOREIGN KEY FK_F6312A78F675F31B');
        $this->addSql('DROP INDEX IDX_F6312A78F675F31B ON coaster');
        $this->addSql('ALTER TABLE coaster DROP author_id');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
    }
}
