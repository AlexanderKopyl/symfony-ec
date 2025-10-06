<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251006085011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users, user_credentials, and user_avatars tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_avatars (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, picture_name VARCHAR(255) DEFAULT NULL, picture_size INT DEFAULT NULL, picture_mime_type VARCHAR(100) DEFAULT NULL, picture_original_name VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_E8C49B2AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_credentials (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(16) NOT NULL, login VARCHAR(50) NOT NULL, password VARCHAR(80) DEFAULT NULL, is_verified TINYINT(1) DEFAULT 0 NOT NULL, verification_code VARCHAR(50) DEFAULT NULL, reset_token VARCHAR(50) DEFAULT NULL, reset_token_expired DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_531EE19BA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, password VARCHAR(80) DEFAULT NULL, email VARCHAR(45) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, google_id VARCHAR(100) DEFAULT NULL, roles JSON NOT NULL, verification_code VARCHAR(255) DEFAULT NULL, verified TINYINT(1) DEFAULT 0 NOT NULL, password_token VARCHAR(50) DEFAULT NULL, password_token_expired DATETIME DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, patronymic VARCHAR(255) DEFAULT NULL, birth_date DATE DEFAULT NULL, gender SMALLINT DEFAULT NULL, oc_customer_id INT DEFAULT NULL, orders_sync_at DATETIME DEFAULT NULL, last_login DATETIME DEFAULT NULL, bonus_points INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_avatars ADD CONSTRAINT FK_E8C49B2AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_credentials ADD CONSTRAINT FK_531EE19BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_avatars DROP FOREIGN KEY FK_E8C49B2AA76ED395');
        $this->addSql('ALTER TABLE user_credentials DROP FOREIGN KEY FK_531EE19BA76ED395');
        $this->addSql('DROP TABLE user_avatars');
        $this->addSql('DROP TABLE user_credentials');
        $this->addSql('DROP TABLE users');
    }
}
