<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180218132152 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, branch VARCHAR(100) NOT NULL, title VARCHAR(100) NOT NULL, type SMALLINT NOT NULL, address VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rate ADD organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE rate ADD CONSTRAINT FK_DFEC3F3932C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_DFEC3F3932C8A3DE ON rate (organization_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rate DROP FOREIGN KEY FK_DFEC3F3932C8A3DE');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP INDEX IDX_DFEC3F3932C8A3DE ON rate');
        $this->addSql('ALTER TABLE rate DROP organization_id');
    }
}
