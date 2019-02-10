<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190210125435 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE watch_list (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE watch_list_movie (watch_list_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_974429F2C4508918 (watch_list_id), INDEX IDX_974429F28F93B6FC (movie_id), PRIMARY KEY(watch_list_id, movie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE watch_list_movie ADD CONSTRAINT FK_974429F2C4508918 FOREIGN KEY (watch_list_id) REFERENCES watch_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE watch_list_movie ADD CONSTRAINT FK_974429F28F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE watch_list_movie DROP FOREIGN KEY FK_974429F2C4508918');
        $this->addSql('DROP TABLE watch_list');
        $this->addSql('DROP TABLE watch_list_movie');
    }
}
