<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191020195357 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_file (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, account_id INT DEFAULT NULL, file_id INT NOT NULL, email VARCHAR(255) NOT NULL, account_type VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, unique_name VARCHAR(255) DEFAULT NULL, administrator TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, user_created TINYINT(1) NOT NULL, user_name VARCHAR(255) DEFAULT NULL, resource_user TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F61E7AD9A76ED395 (user_id), INDEX IDX_F61E7AD99B6B5FBA (account_id), INDEX IDX_F61E7AD993CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_file ADD CONSTRAINT FK_F61E7AD9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_file ADD CONSTRAINT FK_F61E7AD99B6B5FBA FOREIGN KEY (account_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_file ADD CONSTRAINT FK_F61E7AD993CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_file');
    }
}
