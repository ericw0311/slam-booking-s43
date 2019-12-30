<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191230185301 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE planification_resource (id INT AUTO_INCREMENT NOT NULL, planification_period_id INT NOT NULL, resource_id INT NOT NULL, user_id INT NOT NULL, oorder SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_78E129F9DCFAF5EA (planification_period_id), INDEX IDX_78E129F989329D25 (resource_id), INDEX IDX_78E129F9A76ED395 (user_id), UNIQUE INDEX uk_planification_resource (planification_period_id, resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE query_booking (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, file_id INT NOT NULL, name VARCHAR(255) NOT NULL, period_type VARCHAR(255) NOT NULL, beginning_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, user_type VARCHAR(255) NOT NULL, resource_type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_AAA6DCFCA76ED395 (user_id), INDEX IDX_AAA6DCFC93CB796C (file_id), UNIQUE INDEX uk_query_booking (file_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, file_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_EA750E8A76ED395 (user_id), INDEX IDX_EA750E893CB796C (file_id), UNIQUE INDEX uk_label (file_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_parameter (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, parameter_group VARCHAR(255) NOT NULL, parameter VARCHAR(255) NOT NULL, parameter_type VARCHAR(255) NOT NULL, integer_value INT DEFAULT NULL, string_value VARCHAR(255) DEFAULT NULL, boolean_value TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2A771CF4A76ED395 (user_id), UNIQUE INDEX uk_user_parameter (user_id, parameter_group, parameter), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking_user (id INT AUTO_INCREMENT NOT NULL, booking_id INT NOT NULL, user_file_id INT NOT NULL, user_id INT NOT NULL, oorder SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9502F4073301C60 (booking_id), INDEX IDX_9502F407CBC66766 (user_file_id), INDEX IDX_9502F407A76ED395 (user_id), UNIQUE INDEX uk_booking_user (booking_id, user_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planification_view_resource (id INT AUTO_INCREMENT NOT NULL, planification_view_user_file_group_id INT NOT NULL, planification_resource_id INT NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_640DF951CCA0EC98 (planification_view_user_file_group_id), INDEX IDX_640DF9514F1FC012 (planification_resource_id), UNIQUE INDEX uk_planification_view_resource (planification_view_user_file_group_id, planification_resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking_line (id INT AUTO_INCREMENT NOT NULL, booking_id INT NOT NULL, planification_id INT NOT NULL, planification_period_id INT NOT NULL, planification_line_id INT NOT NULL, resource_id INT NOT NULL, timetable_id INT NOT NULL, timetable_line_id INT NOT NULL, user_id INT NOT NULL, ddate DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C98596B83301C60 (booking_id), INDEX IDX_C98596B8E65142C2 (planification_id), INDEX IDX_C98596B8DCFAF5EA (planification_period_id), INDEX IDX_C98596B85CEC22BB (planification_line_id), INDEX IDX_C98596B889329D25 (resource_id), INDEX IDX_C98596B8CC306847 (timetable_id), INDEX IDX_C98596B8CC1B3F3C (timetable_line_id), INDEX IDX_C98596B8A76ED395 (user_id), UNIQUE INDEX uk_booking_line (resource_id, ddate, timetable_id, timetable_line_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking_label (id INT AUTO_INCREMENT NOT NULL, booking_id INT NOT NULL, label_id INT NOT NULL, user_id INT NOT NULL, oorder SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9FDBAD5D3301C60 (booking_id), INDEX IDX_9FDBAD5D33B92F39 (label_id), INDEX IDX_9FDBAD5DA76ED395 (user_id), UNIQUE INDEX uk_booking_label (booking_id, label_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8C9F3610A76ED395 (user_id), UNIQUE INDEX uk_file (user_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_parameter (id INT AUTO_INCREMENT NOT NULL, file_id INT NOT NULL, user_id INT NOT NULL, parameter_group VARCHAR(255) NOT NULL, parameter VARCHAR(255) NOT NULL, parameter_type VARCHAR(255) NOT NULL, integer_value INT DEFAULT NULL, string_value VARCHAR(255) DEFAULT NULL, boolean_value TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A4982E6693CB796C (file_id), INDEX IDX_A4982E66A76ED395 (user_id), UNIQUE INDEX uk_file_parameter (file_id, parameter_group, parameter), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timetable (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, file_id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6B1F670A76ED395 (user_id), INDEX IDX_6B1F67093CB796C (file_id), UNIQUE INDEX uk_timetable (file_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_file_group (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, file_id INT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6024321EA76ED395 (user_id), INDEX IDX_6024321E93CB796C (file_id), UNIQUE INDEX uk_user_file_group (file_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_file_group_user_file (user_file_group_id INT NOT NULL, user_file_id INT NOT NULL, INDEX IDX_74A8A282A18AFB8A (user_file_group_id), INDEX IDX_74A8A282CBC66766 (user_file_id), PRIMARY KEY(user_file_group_id, user_file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planification_view_user_file_group (id INT AUTO_INCREMENT NOT NULL, planification_period_id INT NOT NULL, user_file_group_id INT NOT NULL, user_id INT NOT NULL, active TINYINT(1) NOT NULL, oorder SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_AA003150DCFAF5EA (planification_period_id), INDEX IDX_AA003150A18AFB8A (user_file_group_id), INDEX IDX_AA003150A76ED395 (user_id), UNIQUE INDEX uk_planification_view_user_file_group (planification_period_id, user_file_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, file_id INT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, internal TINYINT(1) NOT NULL, code VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_FFC02E1BA76ED395 (user_id), INDEX IDX_FFC02E1B93CB796C (file_id), UNIQUE INDEX uk_planification (file_id, type, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, user_name VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, account_type VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, unique_name VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64924A232CF (user_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timetable_line (id INT AUTO_INCREMENT NOT NULL, timetable_id INT NOT NULL, user_id INT NOT NULL, type VARCHAR(255) NOT NULL, beginning_time TIME NOT NULL, end_time TIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_44285EF8CC306847 (timetable_id), INDEX IDX_44285EF8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource_classification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, file_id INT NOT NULL, internal TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_37255992A76ED395 (user_id), INDEX IDX_3725599293CB796C (file_id), UNIQUE INDEX uk_resource_classification (file_id, internal, type, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource (id INT AUTO_INCREMENT NOT NULL, classification_id INT DEFAULT NULL, user_id INT NOT NULL, file_id INT NOT NULL, internal TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_BC91F4162A86559F (classification_id), INDEX IDX_BC91F416A76ED395 (user_id), INDEX IDX_BC91F41693CB796C (file_id), UNIQUE INDEX uk_resource (file_id, type, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planification_period (id INT AUTO_INCREMENT NOT NULL, planification_id INT NOT NULL, user_id INT NOT NULL, beginning_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_53E4BB15E65142C2 (planification_id), INDEX IDX_53E4BB15A76ED395 (user_id), UNIQUE INDEX uk_planification_period (planification_id, beginning_date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planification_line (id INT AUTO_INCREMENT NOT NULL, planification_period_id INT NOT NULL, timetable_id INT DEFAULT NULL, user_id INT NOT NULL, week_day VARCHAR(255) NOT NULL, oorder SMALLINT NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D967D141DCFAF5EA (planification_period_id), INDEX IDX_D967D141CC306847 (timetable_id), INDEX IDX_D967D141A76ED395 (user_id), UNIQUE INDEX uk_planification_line (planification_period_id, week_day), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, planification_id INT NOT NULL, resource_id INT NOT NULL, form_note_id INT DEFAULT NULL, user_id INT NOT NULL, file_id INT NOT NULL, note LONGTEXT DEFAULT NULL, beginning_date DATETIME NOT NULL, end_date DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E00CEDDEE65142C2 (planification_id), INDEX IDX_E00CEDDE89329D25 (resource_id), UNIQUE INDEX UNIQ_E00CEDDE1781686E (form_note_id), INDEX IDX_E00CEDDEA76ED395 (user_id), INDEX IDX_E00CEDDE93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, note LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_CFBDFA14A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_file (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, account_id INT DEFAULT NULL, file_id INT NOT NULL, resource_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, account_type VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, unique_name VARCHAR(255) DEFAULT NULL, administrator TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, user_created TINYINT(1) NOT NULL, user_name VARCHAR(255) DEFAULT NULL, resource_user TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F61E7AD9A76ED395 (user_id), INDEX IDX_F61E7AD99B6B5FBA (account_id), INDEX IDX_F61E7AD993CB796C (file_id), UNIQUE INDEX UNIQ_F61E7AD989329D25 (resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking_duplication (id INT AUTO_INCREMENT NOT NULL, origin_booking_id INT NOT NULL, new_booking_id INT NOT NULL, user_id INT NOT NULL, gap INT NOT NULL, ddate DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_14BE610EBAEB462D (origin_booking_id), UNIQUE INDEX UNIQ_14BE610EDE0FFFAC (new_booking_id), INDEX IDX_14BE610EA76ED395 (user_id), UNIQUE INDEX uk_booking_duplication (origin_booking_id, ddate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE planification_resource ADD CONSTRAINT FK_78E129F9DCFAF5EA FOREIGN KEY (planification_period_id) REFERENCES planification_period (id)');
        $this->addSql('ALTER TABLE planification_resource ADD CONSTRAINT FK_78E129F989329D25 FOREIGN KEY (resource_id) REFERENCES resource (id)');
        $this->addSql('ALTER TABLE planification_resource ADD CONSTRAINT FK_78E129F9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE query_booking ADD CONSTRAINT FK_AAA6DCFCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE query_booking ADD CONSTRAINT FK_AAA6DCFC93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE label ADD CONSTRAINT FK_EA750E8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE label ADD CONSTRAINT FK_EA750E893CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user_parameter ADD CONSTRAINT FK_2A771CF4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE booking_user ADD CONSTRAINT FK_9502F4073301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE booking_user ADD CONSTRAINT FK_9502F407CBC66766 FOREIGN KEY (user_file_id) REFERENCES user_file (id)');
        $this->addSql('ALTER TABLE booking_user ADD CONSTRAINT FK_9502F407A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE planification_view_resource ADD CONSTRAINT FK_640DF951CCA0EC98 FOREIGN KEY (planification_view_user_file_group_id) REFERENCES planification_view_user_file_group (id)');
        $this->addSql('ALTER TABLE planification_view_resource ADD CONSTRAINT FK_640DF9514F1FC012 FOREIGN KEY (planification_resource_id) REFERENCES planification_resource (id)');
        $this->addSql('ALTER TABLE booking_line ADD CONSTRAINT FK_C98596B83301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE booking_line ADD CONSTRAINT FK_C98596B8E65142C2 FOREIGN KEY (planification_id) REFERENCES planification (id)');
        $this->addSql('ALTER TABLE booking_line ADD CONSTRAINT FK_C98596B8DCFAF5EA FOREIGN KEY (planification_period_id) REFERENCES planification_period (id)');
        $this->addSql('ALTER TABLE booking_line ADD CONSTRAINT FK_C98596B85CEC22BB FOREIGN KEY (planification_line_id) REFERENCES planification_line (id)');
        $this->addSql('ALTER TABLE booking_line ADD CONSTRAINT FK_C98596B889329D25 FOREIGN KEY (resource_id) REFERENCES resource (id)');
        $this->addSql('ALTER TABLE booking_line ADD CONSTRAINT FK_C98596B8CC306847 FOREIGN KEY (timetable_id) REFERENCES timetable (id)');
        $this->addSql('ALTER TABLE booking_line ADD CONSTRAINT FK_C98596B8CC1B3F3C FOREIGN KEY (timetable_line_id) REFERENCES timetable_line (id)');
        $this->addSql('ALTER TABLE booking_line ADD CONSTRAINT FK_C98596B8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE booking_label ADD CONSTRAINT FK_9FDBAD5D3301C60 FOREIGN KEY (booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE booking_label ADD CONSTRAINT FK_9FDBAD5D33B92F39 FOREIGN KEY (label_id) REFERENCES label (id)');
        $this->addSql('ALTER TABLE booking_label ADD CONSTRAINT FK_9FDBAD5DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file_parameter ADD CONSTRAINT FK_A4982E6693CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE file_parameter ADD CONSTRAINT FK_A4982E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE timetable ADD CONSTRAINT FK_6B1F670A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE timetable ADD CONSTRAINT FK_6B1F67093CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user_file_group ADD CONSTRAINT FK_6024321EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_file_group ADD CONSTRAINT FK_6024321E93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user_file_group_user_file ADD CONSTRAINT FK_74A8A282A18AFB8A FOREIGN KEY (user_file_group_id) REFERENCES user_file_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_file_group_user_file ADD CONSTRAINT FK_74A8A282CBC66766 FOREIGN KEY (user_file_id) REFERENCES user_file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planification_view_user_file_group ADD CONSTRAINT FK_AA003150DCFAF5EA FOREIGN KEY (planification_period_id) REFERENCES planification_period (id)');
        $this->addSql('ALTER TABLE planification_view_user_file_group ADD CONSTRAINT FK_AA003150A18AFB8A FOREIGN KEY (user_file_group_id) REFERENCES user_file_group (id)');
        $this->addSql('ALTER TABLE planification_view_user_file_group ADD CONSTRAINT FK_AA003150A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE planification ADD CONSTRAINT FK_FFC02E1BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE planification ADD CONSTRAINT FK_FFC02E1B93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE timetable_line ADD CONSTRAINT FK_44285EF8CC306847 FOREIGN KEY (timetable_id) REFERENCES timetable (id)');
        $this->addSql('ALTER TABLE timetable_line ADD CONSTRAINT FK_44285EF8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE resource_classification ADD CONSTRAINT FK_37255992A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE resource_classification ADD CONSTRAINT FK_3725599293CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_BC91F4162A86559F FOREIGN KEY (classification_id) REFERENCES resource_classification (id)');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_BC91F416A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_BC91F41693CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE planification_period ADD CONSTRAINT FK_53E4BB15E65142C2 FOREIGN KEY (planification_id) REFERENCES planification (id)');
        $this->addSql('ALTER TABLE planification_period ADD CONSTRAINT FK_53E4BB15A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE planification_line ADD CONSTRAINT FK_D967D141DCFAF5EA FOREIGN KEY (planification_period_id) REFERENCES planification_period (id)');
        $this->addSql('ALTER TABLE planification_line ADD CONSTRAINT FK_D967D141CC306847 FOREIGN KEY (timetable_id) REFERENCES timetable (id)');
        $this->addSql('ALTER TABLE planification_line ADD CONSTRAINT FK_D967D141A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEE65142C2 FOREIGN KEY (planification_id) REFERENCES planification (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE89329D25 FOREIGN KEY (resource_id) REFERENCES resource (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE1781686E FOREIGN KEY (form_note_id) REFERENCES note (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_file ADD CONSTRAINT FK_F61E7AD9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_file ADD CONSTRAINT FK_F61E7AD99B6B5FBA FOREIGN KEY (account_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_file ADD CONSTRAINT FK_F61E7AD993CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user_file ADD CONSTRAINT FK_F61E7AD989329D25 FOREIGN KEY (resource_id) REFERENCES resource (id)');
        $this->addSql('ALTER TABLE booking_duplication ADD CONSTRAINT FK_14BE610EBAEB462D FOREIGN KEY (origin_booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE booking_duplication ADD CONSTRAINT FK_14BE610EDE0FFFAC FOREIGN KEY (new_booking_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE booking_duplication ADD CONSTRAINT FK_14BE610EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planification_view_resource DROP FOREIGN KEY FK_640DF9514F1FC012');
        $this->addSql('ALTER TABLE booking_label DROP FOREIGN KEY FK_9FDBAD5D33B92F39');
        $this->addSql('ALTER TABLE query_booking DROP FOREIGN KEY FK_AAA6DCFC93CB796C');
        $this->addSql('ALTER TABLE label DROP FOREIGN KEY FK_EA750E893CB796C');
        $this->addSql('ALTER TABLE file_parameter DROP FOREIGN KEY FK_A4982E6693CB796C');
        $this->addSql('ALTER TABLE timetable DROP FOREIGN KEY FK_6B1F67093CB796C');
        $this->addSql('ALTER TABLE user_file_group DROP FOREIGN KEY FK_6024321E93CB796C');
        $this->addSql('ALTER TABLE planification DROP FOREIGN KEY FK_FFC02E1B93CB796C');
        $this->addSql('ALTER TABLE resource_classification DROP FOREIGN KEY FK_3725599293CB796C');
        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_BC91F41693CB796C');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE93CB796C');
        $this->addSql('ALTER TABLE user_file DROP FOREIGN KEY FK_F61E7AD993CB796C');
        $this->addSql('ALTER TABLE booking_line DROP FOREIGN KEY FK_C98596B8CC306847');
        $this->addSql('ALTER TABLE timetable_line DROP FOREIGN KEY FK_44285EF8CC306847');
        $this->addSql('ALTER TABLE planification_line DROP FOREIGN KEY FK_D967D141CC306847');
        $this->addSql('ALTER TABLE user_file_group_user_file DROP FOREIGN KEY FK_74A8A282A18AFB8A');
        $this->addSql('ALTER TABLE planification_view_user_file_group DROP FOREIGN KEY FK_AA003150A18AFB8A');
        $this->addSql('ALTER TABLE planification_view_resource DROP FOREIGN KEY FK_640DF951CCA0EC98');
        $this->addSql('ALTER TABLE booking_line DROP FOREIGN KEY FK_C98596B8E65142C2');
        $this->addSql('ALTER TABLE planification_period DROP FOREIGN KEY FK_53E4BB15E65142C2');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEE65142C2');
        $this->addSql('ALTER TABLE planification_resource DROP FOREIGN KEY FK_78E129F9A76ED395');
        $this->addSql('ALTER TABLE query_booking DROP FOREIGN KEY FK_AAA6DCFCA76ED395');
        $this->addSql('ALTER TABLE label DROP FOREIGN KEY FK_EA750E8A76ED395');
        $this->addSql('ALTER TABLE user_parameter DROP FOREIGN KEY FK_2A771CF4A76ED395');
        $this->addSql('ALTER TABLE booking_user DROP FOREIGN KEY FK_9502F407A76ED395');
        $this->addSql('ALTER TABLE booking_line DROP FOREIGN KEY FK_C98596B8A76ED395');
        $this->addSql('ALTER TABLE booking_label DROP FOREIGN KEY FK_9FDBAD5DA76ED395');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610A76ED395');
        $this->addSql('ALTER TABLE file_parameter DROP FOREIGN KEY FK_A4982E66A76ED395');
        $this->addSql('ALTER TABLE timetable DROP FOREIGN KEY FK_6B1F670A76ED395');
        $this->addSql('ALTER TABLE user_file_group DROP FOREIGN KEY FK_6024321EA76ED395');
        $this->addSql('ALTER TABLE planification_view_user_file_group DROP FOREIGN KEY FK_AA003150A76ED395');
        $this->addSql('ALTER TABLE planification DROP FOREIGN KEY FK_FFC02E1BA76ED395');
        $this->addSql('ALTER TABLE timetable_line DROP FOREIGN KEY FK_44285EF8A76ED395');
        $this->addSql('ALTER TABLE resource_classification DROP FOREIGN KEY FK_37255992A76ED395');
        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_BC91F416A76ED395');
        $this->addSql('ALTER TABLE planification_period DROP FOREIGN KEY FK_53E4BB15A76ED395');
        $this->addSql('ALTER TABLE planification_line DROP FOREIGN KEY FK_D967D141A76ED395');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEA76ED395');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14A76ED395');
        $this->addSql('ALTER TABLE user_file DROP FOREIGN KEY FK_F61E7AD9A76ED395');
        $this->addSql('ALTER TABLE user_file DROP FOREIGN KEY FK_F61E7AD99B6B5FBA');
        $this->addSql('ALTER TABLE booking_duplication DROP FOREIGN KEY FK_14BE610EA76ED395');
        $this->addSql('ALTER TABLE booking_line DROP FOREIGN KEY FK_C98596B8CC1B3F3C');
        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_BC91F4162A86559F');
        $this->addSql('ALTER TABLE planification_resource DROP FOREIGN KEY FK_78E129F989329D25');
        $this->addSql('ALTER TABLE booking_line DROP FOREIGN KEY FK_C98596B889329D25');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE89329D25');
        $this->addSql('ALTER TABLE user_file DROP FOREIGN KEY FK_F61E7AD989329D25');
        $this->addSql('ALTER TABLE planification_resource DROP FOREIGN KEY FK_78E129F9DCFAF5EA');
        $this->addSql('ALTER TABLE booking_line DROP FOREIGN KEY FK_C98596B8DCFAF5EA');
        $this->addSql('ALTER TABLE planification_view_user_file_group DROP FOREIGN KEY FK_AA003150DCFAF5EA');
        $this->addSql('ALTER TABLE planification_line DROP FOREIGN KEY FK_D967D141DCFAF5EA');
        $this->addSql('ALTER TABLE booking_line DROP FOREIGN KEY FK_C98596B85CEC22BB');
        $this->addSql('ALTER TABLE booking_user DROP FOREIGN KEY FK_9502F4073301C60');
        $this->addSql('ALTER TABLE booking_line DROP FOREIGN KEY FK_C98596B83301C60');
        $this->addSql('ALTER TABLE booking_label DROP FOREIGN KEY FK_9FDBAD5D3301C60');
        $this->addSql('ALTER TABLE booking_duplication DROP FOREIGN KEY FK_14BE610EBAEB462D');
        $this->addSql('ALTER TABLE booking_duplication DROP FOREIGN KEY FK_14BE610EDE0FFFAC');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE1781686E');
        $this->addSql('ALTER TABLE booking_user DROP FOREIGN KEY FK_9502F407CBC66766');
        $this->addSql('ALTER TABLE user_file_group_user_file DROP FOREIGN KEY FK_74A8A282CBC66766');
        $this->addSql('DROP TABLE planification_resource');
        $this->addSql('DROP TABLE query_booking');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE user_parameter');
        $this->addSql('DROP TABLE booking_user');
        $this->addSql('DROP TABLE planification_view_resource');
        $this->addSql('DROP TABLE booking_line');
        $this->addSql('DROP TABLE booking_label');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE file_parameter');
        $this->addSql('DROP TABLE timetable');
        $this->addSql('DROP TABLE user_file_group');
        $this->addSql('DROP TABLE user_file_group_user_file');
        $this->addSql('DROP TABLE planification_view_user_file_group');
        $this->addSql('DROP TABLE planification');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE timetable_line');
        $this->addSql('DROP TABLE resource_classification');
        $this->addSql('DROP TABLE resource');
        $this->addSql('DROP TABLE planification_period');
        $this->addSql('DROP TABLE planification_line');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE user_file');
        $this->addSql('DROP TABLE booking_duplication');
    }
}
