<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200113041211 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE planification_view_resource (id INT AUTO_INCREMENT NOT NULL, planification_view_user_file_group_id INT NOT NULL, planification_resource_id INT NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_640DF951CCA0EC98 (planification_view_user_file_group_id), INDEX IDX_640DF9514F1FC012 (planification_resource_id), UNIQUE INDEX uk_planification_view_resource (planification_view_user_file_group_id, planification_resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_file_group (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, file_id INT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6024321EA76ED395 (user_id), INDEX IDX_6024321E93CB796C (file_id), UNIQUE INDEX uk_user_file_group (file_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_file_group_user_file (user_file_group_id INT NOT NULL, user_file_id INT NOT NULL, INDEX IDX_74A8A282A18AFB8A (user_file_group_id), INDEX IDX_74A8A282CBC66766 (user_file_id), PRIMARY KEY(user_file_group_id, user_file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planification_view_user_file_group (id INT AUTO_INCREMENT NOT NULL, planification_period_id INT NOT NULL, user_file_group_id INT NOT NULL, user_id INT NOT NULL, active TINYINT(1) NOT NULL, oorder SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_AA003150DCFAF5EA (planification_period_id), INDEX IDX_AA003150A18AFB8A (user_file_group_id), INDEX IDX_AA003150A76ED395 (user_id), UNIQUE INDEX uk_planification_view_user_file_group (planification_period_id, user_file_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE planification_view_resource ADD CONSTRAINT FK_640DF951CCA0EC98 FOREIGN KEY (planification_view_user_file_group_id) REFERENCES planification_view_user_file_group (id)');
        $this->addSql('ALTER TABLE planification_view_resource ADD CONSTRAINT FK_640DF9514F1FC012 FOREIGN KEY (planification_resource_id) REFERENCES planification_resource (id)');
        $this->addSql('ALTER TABLE user_file_group ADD CONSTRAINT FK_6024321EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_file_group ADD CONSTRAINT FK_6024321E93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user_file_group_user_file ADD CONSTRAINT FK_74A8A282A18AFB8A FOREIGN KEY (user_file_group_id) REFERENCES user_file_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_file_group_user_file ADD CONSTRAINT FK_74A8A282CBC66766 FOREIGN KEY (user_file_id) REFERENCES user_file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planification_view_user_file_group ADD CONSTRAINT FK_AA003150DCFAF5EA FOREIGN KEY (planification_period_id) REFERENCES planification_period (id)');
        $this->addSql('ALTER TABLE planification_view_user_file_group ADD CONSTRAINT FK_AA003150A18AFB8A FOREIGN KEY (user_file_group_id) REFERENCES user_file_group (id)');
        $this->addSql('ALTER TABLE planification_view_user_file_group ADD CONSTRAINT FK_AA003150A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_parameter ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, CHANGE user_name user_name VARCHAR(180) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE booking_duplication DROP INDEX IDX_14BE610EDE0FFFAC, ADD UNIQUE INDEX UNIQ_14BE610EDE0FFFAC (new_booking_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_file_group_user_file DROP FOREIGN KEY FK_74A8A282A18AFB8A');
        $this->addSql('ALTER TABLE planification_view_user_file_group DROP FOREIGN KEY FK_AA003150A18AFB8A');
        $this->addSql('ALTER TABLE planification_view_resource DROP FOREIGN KEY FK_640DF951CCA0EC98');
        $this->addSql('DROP TABLE planification_view_resource');
        $this->addSql('DROP TABLE user_file_group');
        $this->addSql('DROP TABLE user_file_group_user_file');
        $this->addSql('DROP TABLE planification_view_user_file_group');
        $this->addSql('ALTER TABLE booking_duplication DROP INDEX UNIQ_14BE610EDE0FFFAC, ADD INDEX IDX_14BE610EDE0FFFAC (new_booking_id)');
        $this->addSql('ALTER TABLE user DROP created_at, DROP updated_at, CHANGE user_name user_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE password password VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE user_parameter DROP created_at, DROP updated_at');
    }
}
