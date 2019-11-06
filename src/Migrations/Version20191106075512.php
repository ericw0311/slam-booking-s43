<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106075512 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_file ADD resource_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_file ADD CONSTRAINT FK_F61E7AD989329D25 FOREIGN KEY (resource_id) REFERENCES resource (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F61E7AD989329D25 ON user_file (resource_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_file DROP FOREIGN KEY FK_F61E7AD989329D25');
        $this->addSql('DROP INDEX UNIQ_F61E7AD989329D25 ON user_file');
        $this->addSql('ALTER TABLE user_file DROP resource_id');
    }
}
