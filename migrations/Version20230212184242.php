<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230212184242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE "currency_dynamic_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "currency_code" (id VARCHAR(255) NOT NULL, parent_code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, eng_name VARCHAR(255) NOT NULL, nominal INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "currency_dynamic" (id INT NOT NULL, currency_id VARCHAR(255) NOT NULL, date DATE NOT NULL, nominal INT NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, access_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql("INSERT INTO \"user\" VALUES (nextval('user_id_seq'), 'test@user.example', '[\"ROLE_ADMIN\"]', '$2a$12$59rAd7F87LmvLyL62Xs4qOh3QPAXWawVitshn9/abZTwrLvSRikc6', '$2a$12$59rAd7F87LmvLyL62Xs4qOh3QPAXWawVitshn9/abZTwrLvSRikc6')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE "currency_dynamic_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE "currency_code"');
        $this->addSql('DROP TABLE "currency_dynamic"');
        $this->addSql('DROP TABLE "user"');
    }
}
