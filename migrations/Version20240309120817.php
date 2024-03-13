<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240309120817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE incidencia ADD user_id INT NOT NULL, ADD fecha DATETIME NOT NULL, CHANGE cliente_id cliente_id INT DEFAULT NULL, CHANGE titulo titulo VARCHAR(255) NOT NULL, CHANGE estado estado VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE incidencia ADD CONSTRAINT FK_C7C6728CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C7C6728CA76ED395 ON incidencia (user_id)');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user ADD rol VARCHAR(255) NOT NULL, DROP roles, CHANGE email email VARCHAR(255) NOT NULL, CHANGE telefono tel VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, ADD telefono VARCHAR(255) NOT NULL, DROP tel, DROP rol, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        $this->addSql('ALTER TABLE incidencia DROP FOREIGN KEY FK_C7C6728CA76ED395');
        $this->addSql('DROP INDEX IDX_C7C6728CA76ED395 ON incidencia');
        $this->addSql('ALTER TABLE incidencia DROP user_id, DROP fecha, CHANGE cliente_id cliente_id INT NOT NULL, CHANGE titulo titulo VARCHAR(255) DEFAULT NULL, CHANGE estado estado VARCHAR(255) DEFAULT NULL');
    }
}
